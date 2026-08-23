# Development and Deployment Tools

This is the executable tool contract for the next implementation session. Commands run from the repository root.

## Pinned toolchain

- PHP 8.4.x
- Composer 2.x
- Node.js 22.x via `fnm`
- pnpm 9.15.9, matching `package.json` and `pnpm-lock.yaml`
- Docker or Google Cloud Build for the production image
- `gcloud` for Cloud Run, Artifact Registry, Scheduler, and GCS
- `uv` only for ARH Python tooling; it is not Tadika's application runtime

## Local setup

```powershell
composer validate --strict
composer install --prefer-dist --no-interaction
Copy-Item .env.example .env
php artisan key:generate
New-Item -ItemType File -Force database/database.sqlite | Out-Null
php artisan migrate:fresh --seed --force
pnpm install --frozen-lockfile
pnpm run build
```

## Local processes

```powershell
php artisan serve --host=127.0.0.1 --port=8000
pnpm run dev
php artisan queue:work --tries=3
php artisan schedule:work
```

Use ARH Server Deploy Bootstrap only for a built/static or running preview surface. It is not the production deployment mechanism.

## Verification commands

```powershell
pnpm run qa:all
pnpm run docs:check
pnpm run doctor
pnpm run qa:ui
vendor/bin/pint --test app config database routes tests
vendor/bin/phpstan --no-progress --memory-limit=512M
php artisan test
npx secretlint "**/*"
npx knip
```

Do not describe PHPStan, secretlint, Knip, or Lighthouse as passing until the command has actually run and its exit code has been recorded.

## Production image

Create a multi-stage Dockerfile with Composer, Node/pnpm asset build, and PHP 8.4 runtime stages. Include PostgreSQL and required document/image extensions, non-root writable `storage` and `bootstrap/cache`, runtime `PORT` handling, and an explicit `/up` health route. Do not bake real secrets or a usable dummy `APP_KEY` into the production runtime.

## Cloud Run setup order

1. Create a dedicated GCP project or obtain approval to use the existing ARH project.
2. Enable Cloud Run, Cloud Build, Scheduler, Secret Manager, Storage, and Artifact Registry APIs.
3. Create a dedicated Neon production project and isolated preview branch.
4. Create a private GCS bucket in `asia-southeast1` and grant the Cloud Run service account object access.
5. Configure Workload Identity Federation for the repository.
6. Build and run the image against SQLite/test Postgres.
7. Deploy a preview revision with preview secrets and run migrations.
8. Verify login, tenant isolation, attendance, assessment append, upload/download, queue, scheduler, and `/up`.
9. Deploy production only after the preview receipt is complete.

## Secret handling

Use SOPS as the primary store and the ARH secret injector as the runtime fallback. Inject `APP_KEY`, Neon URLs, mail/payment/WhatsApp credentials, and storage credentials at runtime. Never request secrets in chat or place them in `.env.example`.
