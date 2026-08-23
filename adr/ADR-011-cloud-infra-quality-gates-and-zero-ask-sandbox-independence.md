# ADR-011: Cloud Infrastructure Quality Gates & Zero-Ask Sandbox Independence

**Status**: Accepted
**Date**: 2026-08-23

## Context

Following the adoption of Google Cloud Run, Neon Serverless PostgreSQL, and Cloudflare R2 (ADR-009 and ADR-010), the application needs automated quality gates to ensure infrastructure invariants are maintained without regressions across CI/CD and deployment workflows.

Furthermore, autonomous AI coding agents operating in ephemeral cloud sandboxes (e.g., Claude Code, GitHub Codespaces, Gitpod) frequently face operational friction when making infrastructure-related commits:
1. **Unreachable Secrets**: Cloud sandboxes do not (and should not) carry live GCP Workload Identity Federation (WIF) credentials, live Neon API tokens, or production Cloudflare R2 API keys.
2. **Missing Pre-installed CLIs**: Ephemeral sandbox containers do not have `gcloud`, `neonctl`, `wrangler`, or `docker` binaries installed.
3. **Flaky Pre-Push Blocking**: If local/pre-push gates require live network queries to GCP Secret Manager or live database probes, the agent is blocked from completing or pushing its task without demanding human operator intervention ("handoff").

## Decision

We establish two complementary architectural layers:

### 1. The 3-Pillar Cloud Infrastructure Quality Gates
We enforce strict invariants across the infrastructure stack:
- **Google Cloud Run Invariants**:
  - Multi-stage Dockerfile using Composer 2, Node 22 (pinned `pnpm@9.15.9`), and FrankenPHP on PHP 8.4 (`dunglas/frankenphp:1-php8.4-bookworm`).
  - Mandatory baked PHP extensions: `pdo_pgsql`, `pgsql`, `pdo_sqlite`, `gd`, `zip`, `intl`, `bcmath`, `opcache`, `pcntl`.
  - GCP Secret Manager references (`--set-secrets`) for all sensitive credentials (`DATABASE_URL`, `APP_KEY`, `AWS_SECRET_ACCESS_KEY`). Direct plaintext `--set-env-vars` for secrets are strictly prohibited.
  - Zero local disk dependency: local storage `/tmp` is ephemeral and cleared across container auto-scaling events.
  - Separation of DDL migrations into a standalone Cloud Run Job (`tadika-migrate`) with `--max-retries=0` and direct DB connection before shifting web traffic.
  - Automated cron scheduling via Cloud Scheduler invoking `tadika-scheduler` on a 1-minute cadence with OIDC authentication.

- **Neon Serverless PostgreSQL Invariants**:
  - Dual connection URL architecture:
    - `DATABASE_URL` uses PgBouncer pooling (`-pooler.neon.tech`) for web traffic and queue workers to avoid connection pool exhaustion under scale.
    - `DB_URL_DIRECT` uses unpooled direct connection for schema migrations and DDL execution.
  - Ephemeral PR Database Branching: GitHub Actions automatically forks an isolated database branch (`preview/pr-<id>-<branch>`) for PR migration checks and drops it upon closure.
  - Credential masking: All dynamically fetched connection strings are masked via `::add-mask::` in CI and never posted in PR comments.

- **Cloudflare R2 Object Storage Invariants**:
  - S3 Flysystem driver configured with private bucket access (`AWS_ENDPOINT=https://<ACCOUNT_ID>.r2.cloudflarestorage.com`, `AWS_DEFAULT_REGION=auto`, `AWS_USE_PATH_STYLE_ENDPOINT=false`).
  - Short-lived presigned URLs for secure document/photo retrieval with zero public bucket egress fees.
  - Streaming file upload/export to prevent `/tmp` memory bloat on Cloud Run instances.

### 2. Zero-Ask Cloud Sandbox Agent Independence
To guarantee that cloud agents can validate, test, commit, and push without secret blockers or missing tools:
- **Separation of Static Audit vs. Live Execution**:
  - **Local/Sandbox Stage (`_qa/tadika-infra-quality-gate.mjs`)**: 100% hermetic static AST, Regex, and configuration inspection. Requires zero live cloud secrets, zero network egress, and zero cloud CLI binaries.
  - **CI/CD Execution Stage (`deploy.yml`, `neon_workflow.yml`)**: GitHub Actions runner executes real provisioning, WIF authentication, Docker builds, and live migrations where secrets are securely injected.
- **Hermetic Test Fallback**:
  - The local test suite (`php artisan test`) defaults to in-memory SQLite (`:memory:`) and fake storage disks (`Storage::fake('s3')`), enabling full test passes inside isolated sandboxes.

## Consequences

- Infrastructure mistakes (such as passing plaintext DB secrets in deploy commands, missing required PHP extensions in the Dockerfile, or misconfiguring R2 endpoints) are caught immediately during static pre-commit / CI gates.
- Ephemeral sandbox agents can autonomously build, lint, test, and push code without being halted by missing cloud credentials or uninstalled platform CLIs.
- Production deployments remain strictly automated, keyless (via GCP WIF), and secure.
