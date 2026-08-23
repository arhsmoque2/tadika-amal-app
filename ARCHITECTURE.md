# Architecture

## Purpose and boundary

Tadika Amal is a multi-school operations system for staff records, cohorts, attendance, configurable assessments, timetables, reports, and controlled future parent access. It is a Laravel monolith for v1, not a collection of microservices.

```text
Teacher/Admin browser
  -> Cloud Run web container
       -> Laravel 13 HTTP kernel
            -> Filament 4 / Livewire panels
            -> policies + school scope
            -> domain actions and read services
            -> Eloquent/PostgreSQL
            -> private object storage
       -> Cloud Run Job / Scheduler for scheduled work
```

The repository currently declares Laravel `^13.0` in `composer.json`; older documents saying Laravel 12 are historical and must not be used as deployment instructions.

## Runtime topology

### Web process

The web process serves Filament, Livewire, Blade, and Vite-built assets. It is stateless: no request may rely on a local file surviving a restart or reaching a different instance.

Preferred production image: PHP 8.4 with Laravel Octane/FrankenPHP, following the proven DPiK pattern. The fallback is PHP-FPM/NGINX if the image, health check, or browser smoke test fails.

### Database

Neon PostgreSQL is the system of record. Production, preview, and development use separate projects or isolated branches with separate credentials. Migrations run from the direct connection; runtime pooling is enabled only after transaction-pooler compatibility is verified.

### Storage

Student photographs, attachments, generated PDF/Office files, and exports use a private GCS bucket in the same region as Cloud Run. Cloudflare R2 is an approved alternative if the operator chooses the ARH-URUS storage pattern. Local storage is development-only.

### Scheduled and asynchronous work

The initial queue driver is the database driver because it avoids adding Redis before there is measured need. A dedicated worker process/job is required for long reports, imports, notifications, and document generation. Cloud Scheduler invokes a Cloud Run Job for `php artisan schedule:run`; scheduled work must be idempotent.

## Domain model

All tenant-owned tables include `school_id` and indexes supporting it. The minimum model groups are:

- Identity: `User`, `Admin`, roles, permissions, audit entries.
- School structure: `School`, `Cohort`, teacher assignments, cohort membership.
- Registry: `Student`, guardian/contact data, profile schema values, photo reference.
- Operations: attendance records, health/incident records when implemented, timetable slots.
- Configuration: profile schemas and assessment schemas with versions.
- History: append-only assessment records, corrections, exports, generated documents.

Foreign keys and composite unique constraints prevent cross-school references. A policy is mandatory even where the database key is present.

## Security boundaries

1. Authentication identifies the actor; authorization identifies the school and cohort scope.
2. Admin may configure school data and view all school records.
3. Teacher may operate only on assigned cohorts.
4. Parent access is future read-only access to explicitly linked children.
5. Object storage is private and served through authorized, short-lived URLs.
6. Secrets are injected by the deployment environment; `.env` files and real student data never enter Git.
7. Production `APP_KEY` is generated once and never rotated without an encryption migration plan.

## Data invariants

- No curriculum-specific columns or enums are built into migrations.
- Assessment records are append-only after commit.
- Attendance uniqueness is school + student + date.
- Archived students, cohorts, and schema fields remain queryable for historical records.
- A failed file upload cannot be represented as a completed attachment.
- Reports and exports execute through authorized read services.

## Deployment topology

```text
GitHub main
  -> CI: PHP, JS, docs, secret, browser gates
  -> Cloud Build / Artifact Registry
  -> Cloud Run revision (asia-southeast1)
       -> Neon (private credential)
       -> GCS (service-account IAM)
  -> Cloud Scheduler -> Cloud Run Job
```

GitHub Actions uses Workload Identity Federation. No long-lived GCP service-account key is committed. Preview deployments use an isolated Neon branch and non-production secrets. Local/Tailnet UI previews use ARH Server Deploy Bootstrap and do not imply production deployment.

## Failure boundaries

- Neon unavailable: reads and writes fail visibly; no local fallback may create divergent school data.
- Storage unavailable: upload/export operation fails and leaves no false success state.
- Worker unavailable: web requests remain usable where possible; queued work is visible as pending and retried.
- Scheduler unavailable: the next scheduled run must be safe to replay.
- New revision unhealthy: Cloud Run keeps the prior healthy revision serving traffic.

## Capacity and evolution

The v1 target is small schools with hundreds of students, not a public high-volume SaaS. Start with one web service and database queue. Add Redis, separate workers, read models, or a parent portal only when a measured workload or accepted capability requires them.
