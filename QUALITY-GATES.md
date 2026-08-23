# Quality Gates

These gates prevent the repository from claiming a capability or deployment that has only been described. Each gate records the command, exit code, and receipt location.

## Gate 0 — documentation truth

```powershell
pnpm run docs:check
```

Required documents exist and are non-hollow: README, ARCHITECTURE, DESIGN, DEVTOOLS, QUALITY-GATES, HANDOFF, AGENTS, ADRs, and capability/intent documents. Claims are marked implemented, configured, verified, or planned.

## Gate 1 — dependency and source integrity

```powershell
composer validate --strict
composer install --prefer-dist --no-interaction
pnpm install --frozen-lockfile
pnpm run build
npx secretlint "**/*"
```

No lockfile drift, exposed secrets, failed asset build, or unreviewed dependency mutation is accepted.

## Gate 2 — PHP correctness

```powershell
Get-ChildItem app,database,config,routes,tests -Filter *.php -Recurse |
  ForEach-Object { php -l $_.FullName }
vendor/bin/pint --test app config database routes tests
vendor/bin/phpstan --no-progress --memory-limit=512M
php artisan test
```

PHPStan is a blocking gate once existing findings are triaged. Until then, its exact finding count is recorded as a limitation; `|| true` is not a pass.

## Gate 3 — database and tenancy

```powershell
Copy-Item .env.example .env -Force
php artisan key:generate
New-Item -ItemType File -Force database/database.sqlite | Out-Null
php artisan migrate:fresh --seed --force
php artisan test --filter=Tenant
```

Tests must prove two schools cannot read or write each other's students, cohorts, attendance, assessments, files, or reports. Test migration rollback where practical and verify seeded demo data.

## Gate 4 — capability behavior

Required feature tests cover profile schema versioning, school-scoped MyKid uniqueness, student deactivation, cohort transfer history, timetable overlap/timezone selection, attendance lock and correction, assessment compilation and append-only correction, query empty states, date-range totals, and role denial paths. Each test maps to a `CAP-*` identifier and a scenario in `SCENARIOS.md`.

## Gate 5 — browser and UI behavior

Run the existing UI gate, then add a real browser smoke test against a running Laravel application. Verify login, teacher scope, attendance touch/keyboard controls, long Malay labels, dynamic assessment fields, timetable empty slots, and console/mixed-content errors.

The current static UI script is useful but does not measure real 44px geometry; do not claim measured touch-target compliance until Playwright geometry checks exist.

## Gate 6 — production container

```powershell
docker build -t tadika-amal:preview .
docker run --rm -p 8080:8080 --env-file .env tadika-amal:preview
Invoke-WebRequest http://127.0.0.1:8080/up
```

If Docker is unavailable, run the equivalent build through Cloud Build and retain the receipt. The image must bind the injected port and serve correctly behind a proxy.

## Gate 7 — preview infrastructure

Use a dedicated Neon preview branch and non-production bucket/prefix. Verify migrations, object upload/download, queue processing, scheduled command, Cloud Run revision health, and rollback. No real student data may be used.

## Gate 8 — handoff completeness

Before ending a session, update HANDOFF.md with the objective, branch/commit, exact commands and results, changed files, blockers, and one cold-start next action.
