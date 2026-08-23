# ADR-009: Production Cloud Architecture — Cloud Run, Neon Branching, Cloudflare R2, and Workload Identity Federation

**Status**: Accepted  
**Date**: 2026-08-23  

---

## Context

Tadika Amal App requires a production-grade, highly available, low-maintenance deployment target that provides:
1. **Zero-cold-start web experience** with automated zero-downtime rolling deploys.
2. **Ephemeral PR preview databases** to validate schema migrations and dynamic JSON fields before merging to `main`.
3. **Zero-egress object storage** for student photos, assessment scans, and generated `.docx`/`.pptx`/`.pdf` document bundles.
4. **Keyless CI/CD authentication** without long-lived Google Cloud service account keys stored in GitHub secrets.

---

## Decision Drivers & Trade-Offs

### 1. Compute & Deployment: Google Cloud Run (`asia-southeast1`)
- **Hosting**: Deployed as a stateless container (PHP 8.4 + FrankenPHP / Octane) in Google Cloud Run Singapore region (`asia-southeast1`).
- **GCP Project**: Colocated inside the established `arh-gcloud-vm` project (`102469945521`) with dedicated service accounts and IAM isolation.
- **CI/CD Authentication**: GitHub Actions deploys keylessly via **Workload Identity Federation (WIF)** (`urus-github-pool` / `tadika-deployer` service account) restricted to `arhsmoque2/tadika-amal-app`.
- **Background Jobs & Cron**: Cloud Scheduler triggers `php artisan schedule:run` against Cloud Run jobs; asynchronous queues leverage the PostgreSQL database queue driver.

### 2. Database: Neon Serverless PostgreSQL with Ephemeral PR Branching
- **Primary Database**: Neon PostgreSQL in Singapore (`ap-southeast-1`) providing autoscaling compute and instant storage.
- **Connection Modes**:
  - **Migrations & CLI**: Direct connection (`DB_URL_DIRECT`) without transaction pooling.
  - **Runtime Web Requests**: Connection pooler (`DB_URL` / `DB_DATABASE_URL`) with pgbouncer compatibility.
- **PR Preview Branching**: Neon GitHub integration (`neondatabase/create-branch-action@v5` in `.github/workflows/neon_workflow.yml`) automatically spawns an isolated database branch for each PR and tears it down upon PR close.

### 3. Object Storage: Cloudflare R2 (ARH-URUS Pattern)
- **Selection**: Cloudflare R2 supersedes Google Cloud Storage (GCS) to eliminate egress and bandwidth costs ($0/GB) on student media downloads and document bundle exports.
- **Laravel Driver**: Standard Flysystem S3 driver (`league/flysystem-aws-s3-v3`) configured to the Cloudflare R2 endpoint (`https://<CLOUDFLARE_ACCOUNT_ID>.r2.cloudflarestorage.com`).
- **Privacy & Security Invariant**: The R2 bucket is strictly private. Media and exports are served exclusively through short-lived presigned URLs generated via `Storage::disk('r2')->temporaryUrl()`.

---

## Consequences & Invariants

1. **Zero Static GCP Keys**: No GCP service account JSON key may ever be created or committed to GitHub Secrets; all deployment steps authenticate via OpenID Connect (OIDC) through WIF.
2. **Tenant DB Boundary**: All migrations and queries enforce `school_id` isolation across Neon branch environments.
3. **Stateless Web Layer**: No file uploads or local state may reside on the ephemeral container filesystem; all media writes must flow to R2.
4. **Automated Verification Gate**: Deployments to Cloud Run only trigger upon green status across all static quality gates, Pint, PHPStan, and Pest test suites.
