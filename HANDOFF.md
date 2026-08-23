# Handoff

## Current state — 2026-08-23

The repository has a documented product boundary and Laravel/Filament application scaffold. This session finalized the target architecture and implementation contracts; it did not provision production infrastructure or claim production readiness.

## Decisions now recorded

- [ADR-009](adr/ADR-009-production-architecture-cloud-run-neon.md): Laravel monolith on Cloud Run with Neon and private object storage.
- [ARCHITECTURE.md](ARCHITECTURE.md): runtime topology, data boundaries, security invariants, and failure behavior.
- [DESIGN.md](DESIGN.md): code-level capability contracts and implementation order.
- [DEVTOOLS.md](DEVTOOLS.md): pinned tools and deployment setup sequence.
- [QUALITY-GATES.md](QUALITY-GATES.md): blocking verification gates and honest limitations.

## Important repository truth

- `composer.json` requires Laravel `^13.0`; older documents that say Laravel 12 are stale.
- The application is currently a Laravel/Filament staff workspace. A parent Vue/Inertia portal is future scope.
- `pnpm run qa:all` and the four `_qa/` scripts are primarily static/content checks. They are not substitutes for runtime, browser, database, or deployment verification.
- CI currently makes PHPStan advisory with `|| true`; remove that exception only after existing findings are triaged.
- Secretlint, Knip, Lighthouse, Docker, Neon, Cloud Run, GCS, Scheduler, and production secrets still need real verification.

## Next session objective

Build and verify the first production-shaped preview without using real student data:

1. Confirm the Laravel version/lockfile and run clean Composer and pnpm installations.
2. Run `migrate:fresh --seed` and record the real result.
3. Inspect migrations/models against the `school_id` and append-only contracts in DESIGN.md.
4. Add or repair runtime feature tests for profile, student, cohort, attendance, assessment, query, and roles.
5. Add the production Dockerfile using PHP 8.4 and the chosen runtime.
6. Build the image locally or with Cloud Build and verify `/up`.
7. Create a disposable Neon preview branch and test migrations with the direct connection.
8. Configure a preview Cloud Run revision and private preview storage.
9. Run a browser smoke test and retain receipts.

## Suggested first commands

```powershell
Set-Location D:\_ARH-AGENT-OS\projects\tadika-amal-app
git status --short --branch
composer validate --strict
composer install --prefer-dist --no-interaction
pnpm install --frozen-lockfile
pnpm run build
Copy-Item .env.example .env -Force
php artisan key:generate
New-Item -ItemType File -Force database/database.sqlite | Out-Null
php artisan migrate:fresh --seed --force
php artisan test
pnpm run docs:check
pnpm run qa:all
```

## Parked blockers

- Production GCP project/service account and Workload Identity Federation are not verified for this repository.
- Production and preview Neon projects/branches are not provisioned for Tadika.
- Object-storage provider has not been selected operationally between GCS and R2.
- Production Docker image has not been built and run.
- Parent portal, payment gateway, WhatsApp delivery, AI assistance, and mail delivery require separate implementation and security decisions.
- Existing code-level implementation must be reconciled with DESIGN.md; documentation is not evidence that a capability is implemented.

## Session closure contract

The next agent must update this file after every meaningful checkpoint with the branch, commit, command, result, and next action. Do not mark deployment complete from a successful build alone; prove the running service and its data boundaries.
