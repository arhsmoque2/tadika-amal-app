# ADR-009: Production architecture — Laravel monolith on Cloud Run with Neon

**Status**: Accepted
**Date**: 2026-08-23
**Scope**: Tadika Amal production and preview environments

## Decision

Tadika Amal will remain a single Laravel application with Filament and Livewire for staff operations. Production compute will run on Google Cloud Run in `asia-southeast1`, with Neon PostgreSQL as the database and private object storage for uploads and generated documents.

```text
Browser
  -> Cloud Run web container (Laravel + Filament/Livewire + Vite assets)
       -> Neon PostgreSQL
       -> private GCS bucket (or R2 if the storage decision is explicitly changed)
       -> Cloud Run Job invoked by Cloud Scheduler for scheduled work
```

The production image will use a multi-stage Docker build and Laravel Octane with FrankenPHP when the runtime verification proves the image. PHP-FPM/NGINX remains the fallback if the same smoke tests fail.

## Why this fits Tadika

- The current application is already a Laravel/Filament staff workspace, not a JavaScript-only application.
- Neon supplies PostgreSQL without operating a database server and supports pooled connections when the application scales.
- Cloud Run matches the stateless web-container model and existing ARH deployment practice.
- Cloud Storage/R2 prevents student photos, attachments, and generated documents from disappearing when containers restart.
- A monolith keeps authorization, school isolation, migrations, queues, and reporting in one transaction boundary.

## Explicit non-decisions

- No Python/MCP service owns Tadika data. ARH-URUS-Work remains an optional operator integration, not the application backend.
- No separate Vue/Inertia parent portal is required for v1. It is a later bounded surface sharing the Laravel domain model.
- No shared Neon database is used across Tadika, ARH-URUS, or DPiK.
- No local filesystem is a production source of truth.
- No automatic payment, WhatsApp, or AI integration is production-ready until its credential, retry, audit, and failure behavior are tested.

## Data and runtime rules

1. Every school-owned table has a `school_id` foreign key and supporting composite uniqueness/indexes.
2. Policies and query scopes enforce school and cohort visibility; UI filtering is not a security boundary.
3. Assessment history is append-only. Corrections create linked records.
4. Sessions and cache must be database-backed or otherwise shared across instances.
5. Neon direct connection strings are used for migrations and administrative operations. Runtime pooling is enabled only after transaction-pooler behavior is verified.
6. Object storage buckets are private; access is through authorization and short-lived signed URLs.
7. Scheduled work runs through Cloud Scheduler/Cloud Run Job or a verified equivalent; it must not depend on one web instance remaining alive.

## Alternatives considered

### Render + Neon

Good for a quick Docker deployment, but it introduces a second hosting convention when ARH already has a verified Cloud Run pattern. It remains a valid fallback for a pilot if GCP credentials or billing are unavailable.

### Cloudflare Pages/Workers

Rejected for the Laravel runtime. The application needs PHP, Filament/Livewire sessions, document generation, queues, and scheduled work.

### VPS or shared hosting

Rejected for the first production deployment because patching, process supervision, backups, and deployment rollback become Tadika-specific operational work.

## Consequences

Positive: one domain model, managed database, stateless deploys, durable files, and reuse of ARH Cloud Run/Neon knowledge.

Cost: GCP setup, IAM, container verification, Cloud Scheduler, object-storage configuration, and production secrets are prerequisites. This ADR is an approved design, not evidence that production infrastructure is provisioned.

## Verification required before calling this deployed

- Build and run the production image locally or in Cloud Build.
- Run migrations against a disposable Neon preview branch.
- Verify `/up`, login, school isolation, attendance save, assessment append, file upload, signed download, queue execution, and scheduled command.
- Confirm no production secret or real student data is present in preview.
