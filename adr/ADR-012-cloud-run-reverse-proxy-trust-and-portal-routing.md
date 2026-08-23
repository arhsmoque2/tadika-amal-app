# ADR-012: Cloud Run Reverse Proxy Trust, Automated Seeding & Portal Routing

**Status**: Accepted
**Date**: 2026-08-24

## Context

Following the initial production rollout on Google Cloud Run (ADR-009, ADR-010, ADR-011), live deployment testing and Playwright browser telemetry surfaced three critical operational friction points:

1. **Reverse Proxy SSL Termination & Livewire Session Authentication**:
   Google Cloud Run terminates SSL at the Google Frontend Load Balancer and forwards incoming requests to the container via internal HTTP, adding the standard `X-Forwarded-Proto: https` and `X-Forwarded-For` headers. Because Laravel 11's default middleware stack did not explicitly configure trusted reverse proxies, Laravel treated incoming requests as unencrypted HTTP. During Livewire POST updates (including authentication at `/app/login` and `/admin/login`), Livewire detected a scheme/origin mismatch between client HTTPS and internal HTTP, triggering HTTP 500 error responses on authentication attempts.

2. **Empty Guest Panel at Root URL (`/`)**:
   The root path (`/`) was registered to `GuestPanelProvider` (`id('guest')`, `path('')`), which exposed an empty dashboard without widgets because public self-registration is disabled for school data privacy. Navigating to the primary production URL presented visitors with an unhelpful blank dashboard canvas instead of routing directly to the active workspace.

3. **Seeder Omission in Automated Deployment Pipeline**:
   The migration Cloud Run Job (`tadika-migrate`) only executed `php artisan migrate --force`. While database tables and indexes were created on Neon Serverless PostgreSQL, initial administrative roles, classes, teachers, and KSPK syllabus frameworks were omitted unless manually triggered, causing empty states in fresh cloud deployments.

## Decision

We establish three core architectural improvements:

### 1. Reverse Proxy Trust Header Configuration
We configure `$middleware->trustProxies(at: '*');` inside `bootstrap/app.php`. This instructs Laravel's `TrustProxies` middleware to unconditionally trust Google Cloud Load Balancer headers (`X-Forwarded-For`, `X-Forwarded-Proto`, `X-Forwarded-Port`, `X-Forwarded-Host`), correctly resolving `$request->secure()` as `true` and ensuring Livewire snapshot signatures and CSRF tokens validate cleanly over HTTPS.

### 2. Primary Portal Redirection
We isolate the guest panel by re-scoping `GuestPanelProvider` to `path('guest')` and configuring an explicit root route redirect in `routes/web.php`:
```php
Route::get('/', function () {
    return redirect('/app/login');
});
```
Visitors to the primary web URL are immediately directed to the active Teacher & Staff Workspace login portal.

### 3. Automated Idempotent Production Seeding & Stderr Logging
We update `.github/workflows/deploy.yml`:
- The `tadika-migrate` Cloud Run Job executes both schema migrations and idempotent seeders:
  ```bash
  php artisan migrate --force && php artisan db:seed --force
  ```
- The Cloud Run service environment is updated to `LOG_CHANNEL=stderr` to stream Laravel error logs and stack traces directly into GCP Cloud Logging for real-time observability.

## Consequences

- **Live Authentication**: Teachers and administrators can authenticate seamlessly without proxy SSL mismatch errors.
- **Immediate First-Load Usability**: Root domain visitors land directly on the relevant login screen.
- **Zero-Touch Provisioning**: New environments and database branches are fully populated with required seed data automatically upon deployment.
- **Verified Observability**: Cloud Run runtime exceptions are captured in Google Cloud Logging stderr streams.
