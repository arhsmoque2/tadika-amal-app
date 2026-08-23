# Handoff

## [WP-STATE] Current State

> Phase 1 (Docs Overhaul & Architecture Alignment), Phase 2 (Base SIS & Compliance Scaffolding), and Phase 3 (Universal Import/Export, Multi-Format Document Generation & AI JSON Pipeline per ADR-008) are fully implemented and verified against the ARH Quality Gate.

### Delivered Document & Architectural Suite:
- [`INTENT.md`](INTENT.md) — Platform philosophy ("MS Word / Instagram for Kindergarten Ops", not prescriptive syllabus).
- [`SCENARIOS.md`](SCENARIOS.md) — 7 comprehensive actor event flows covering profiles, attendance, dynamic assessment, timetable, and querying.
- [`CAPABILITIES.md`](CAPABILITIES.md) — 8 platform capabilities derived directly from actor needs.
- [`ARCHITECTURE.md`](ARCHITECTURE.md) — Monolithic Laravel 12 + Filament v4 data model, dynamic schema engine, and security invariants.
- [`DESIGN.md`](DESIGN.md) — Screen contracts for attendance sheet, dynamic assessment recorder, and timetable grid.
- [`IMPLEMENTATION.md`](IMPLEMENTATION.md) — 6 vertical execution phases with clear boundaries.
- [`DEVTOOLS.md`](DEVTOOLS.md) — Pinned toolchain and dev commands.
- [`RECIPES.md`](RECIPES.md) — Copy-paste runbooks for setup, serving, seeding, and testing.
- [`QUALITY-GATES.md`](QUALITY-GATES.md) — Local, docs, and dynamic schema safety criteria.
- [`GAPS.md`](GAPS.md) — Open design decisions and parked scope (Parent Portal, Billing in v2).
- [`GOTCHAS.md`](GOTCHAS.md) — 5 failure capsules (including anti-pattern of hardcoded curriculum).
- [`docs/pattern-research.md`](docs/pattern-research.md) — In-depth audit and scorecard across 8 candidate repositories.
- [`adr/`](adr/) — 8 Architecture Decision Records:
  - `ADR-001`: Laravel 12 Application Framework.
  - `ADR-002`: Filament v4 Admin Panel (v1 Scoped).
  - `ADR-003`: JSON Schema for Runtime-Configurable Fields.
  - `ADR-004`: Append-Only Assessment Session Records.
  - `ADR-005`: Starter Kit Selection & Scaffolding Accelerators (`filakitv4` + `filament-crud-maker` + `filament-shield`).
  - `ADR-006`: Academico SIS Adaptation for Timetables, Attendance Matrix, Document Pipeline & KSPK Assessment.
  - `ADR-007`: Preschool Operational Extensions & Regulatory Compliance Suite (WhatsApp Broadcaster, Saringan Pagi, RPH Planner, LHDN Invoicing, JKM Incident Log).
  - `ADR-008`: Universal Multi-Module Import, Tri-Format Export, Presentation Generator, and AI-Ready Diagnostic Pipeline.

### Delivered Services & Reporting Pipelines:
1. **Universal Import & Export**:
   - `UniversalImportExportService.php`: Model-first schema definition contracts for students, teachers, timetables, and fee invoices.
   - `AttendanceSpreadsheetService.php`: CSV / Excel cohort attendance matrix export.
2. **Templated Word (`.docx`) Documents**:
   - `WordTemplateExportService.php`: Dynamic `${token}` replacement and nested tables for admission offer letters and JKM incident logs.
   - `DocumentTemplateService.php`: Student registration forms & admission documents.
3. **PowerPoint Presentations (`.pptx`)**:
   - `PresentationDeckService.php`: Automated slide deck generation for parent orientation briefings, weekly RPH thematic lesson decks, and AGM reports.
4. **PDF Reports & Visual Posters**:
   - `AssessmentReportPdfService.php`: Official annual KSPK developmental progress report cards via mPDF & Blade.
   - `FeeReceiptPdfService.php`: LHDN Section 46(1)(r) statutory preschool tax relief fee receipts.
   - `PosterDocumentService.php`: Daily morning health bulletin noticeboard posters, student certificates of achievement, and event flyers.
5. **AI Chat & Diagnostic Bridge**:
   - `AiReportDiagnosticService.php`: Structured JSON evaluation payload generator for AI Teacher Co-Pilot (qualitative *Ulasan Perkembangan Murid*) and administrative sentinel operational diagnostics.

---

## [WP-MENTAL-MODEL] The Immutable Invariant

> **The platform provides the containers; the school provides the content.**
> Never hardcode curriculum names (*Iqra'*, *Hafazan*, *Solat*, specific subjects) as rigid database columns. They are configured dynamically by the school at runtime using the schema engine.

---

## [WP-NEXT] Next Immediate Action

`feat/universal-import-export-and-doc-engine` merged as PR #2, plus two follow-ups (PR #3 docs, PR #4 gate hardening). CI is green end-to-end on `main` for the first time. What's below is the handoff for whoever picks this up on a **local machine with unrestricted network access** — several things could only be partially verified from the cloud sandbox that did this round of fixes, and this section says exactly which, and why.

---

## [WP-LOCAL-AGENT-HANDOFF] Handoff for Local Pickup

### A. Quality gates that check filenames, not reality

All four scripts in `_qa/` (`cloud-sandbox-independence-gate.mjs`, `tadika-ui-ux-quality-gate.mjs`, `tadika-docs-doctor.mjs`, `tadika-quality-doctor.mjs`) are **static file-content scans only** — `fs.readFileSync` + regex/JSON checks, zero dependencies, zero subprocess calls. That's a legitimate and fast first line of defense (see `_qa/cloud-sandbox-independence-gate.mjs`'s Gates 7-9, added in PR #4, which catch real regressions this way — lockfile drift, duplicate migration columns, Filament property-type mismatches). But three tools that are **configured in this repo and referenced by name in `AGENTS.md`'s DevTool table are never actually invoked anywhere**:

| Tool | Config present | Actually run? | What runs instead |
| :--- | :--- | :--- | :--- |
| `secretlint` | `.secretlintrc.json`, `.secretlintignore` | **No** — grep confirms zero invocations outside its own config file | A hand-rolled 4-pattern regex scanner in `tadika-quality-doctor.mjs` (AWS keys, `ghp_`, PEM headers, `sk-` keys only — no generic entropy check, no `.env`-shaped-value detection) |
| `knip` | `knip.json` | **No** — only its filename/JSON-validity is checked | Nothing — dead code and orphan npm deps are unaudited |
| Lighthouse CI | `lighthouserc.json` | **No** — only its filename/JSON-validity is checked | Nothing — the "performance ≥0.85, a11y ≥0.95, PWA offline" thresholds `AGENTS.md` cites are aspirational, not enforced |

Also: `.github/workflows/ci.yml` runs `./vendor/bin/phpstan --no-progress --error-format=github || true` — PHPStan output is currently advisory only and can never fail the build, regardless of what it reports.

**What to do, in order of value:**
1. Run `./vendor/bin/phpstan --no-progress` for real and look at what it actually says. If it's clean (or can be made clean / baselined for pre-existing debt), remove `|| true` from `ci.yml` so it actually gates merges.
2. Run `npx secretlint "**/*"` for real. If clean, add it as a CI step (or call it from `tadika-quality-doctor.mjs` via `child_process` instead of hand-rolled regex — the regex version misses anything shaped like a real Laravel `APP_KEY`, a database password, or a webhook URL with an embedded token).
3. Run `npx knip` for real, triage the findings, wire it into CI once it's not noisy.
4. Decide if Lighthouse CI is worth wiring up (needs a served build, i.e. `php artisan serve` + `pnpm run build` + `lhci autorun` — heavier to run in CI than the others). If not worth it right now, remove the unused config and the claim from `AGENTS.md` rather than leave a false promise.
5. `_qa/tadika-ui-ux-quality-gate.mjs`'s "Gate 2: Mobile Touch Targets" doesn't measure any pixel dimension — it checks that certain Malay status-label strings (`hadir`, `tidak_hadir`, etc.) appear in one specific Blade file. The `≥44px×44px` touch-target claim in `AGENTS.md` §Step 1 is not actually checked by anything. If that constraint matters, it needs a real DOM/CSS measurement (Playwright + `getBoundingClientRect`, or a Puppeteer screenshot-diff), not a string-presence check.

### B. Total sandbox independence — what's still unverified

This round of work was done from a remote sandbox whose outbound proxy could not complete `composer install` for a subset of packages (`pestphp/*`, `phpoffice/*`, `wallacemartinss/filament-icon-picker`) — every attempt hit `Could not authenticate against github.com` on GitHub's zipball API, and the proxy's injected token wasn't a usable OAuth PAT. Real GitHub Actions runners don't have this problem: CI's own `composer install --prefer-dist` installs all 190 locked packages (131 runtime + 59 dev) cleanly every run, confirming this was an artifact of that one sandbox's network policy, not a repo defect. But it means several things were verified **only on GitHub Actions**, never on a real local dev machine, and a local agent should close that gap:

1. **Clean-room `composer install`**: delete `vendor/`, run `composer install --prefer-dist` with no pre-warmed cache, confirm it completes with zero manual intervention. (This session had to manually splice `filament/*` v4.12.6 source from a cached git mirror into `vendor/` just to test-build the frontend — that workaround should never be necessary on a real machine.)
2. **Clean-room `pnpm install && pnpm run build`**: delete `node_modules/` and the pnpm store, confirm `pnpm install --frozen-lockfile && pnpm run build` produces `public/build/manifest.json` with no manual fixes. (This was verified in this round, but only after manually vendoring Filament's CSS source — worth re-confirming from a genuinely clean tree.)
3. **`php artisan migrate:fresh --seed`**: this round only ever ran `php artisan migrate --force` once against a fresh empty SQLite file. Nobody has verified `migrate:fresh` (drop-and-recreate) or that every migration's `down()` method actually reverses cleanly, or that `TadikaAmalKspkSeeder` runs without error against the current schema.
4. **`packages/filament-crud-maker`**: vendored in PR #2 (ERR-018) to fix the dangling-submodule bug. Confirmed: it's PSR-4 autoloaded (`Freis\FilamentCrudGenerator\` → `packages/filament-crud-maker/src/` in `composer.json`), but `grep -rl FilamentCrudGenerator app/ routes/` finds **zero call sites** — it is currently dead vendored code that only exists to satisfy Gate 3's "non-empty directory" check. Either wire it into an actual `artisan make:filament-crud`-style command, or drop it and the gate check for it — a dependency nothing calls is attack surface and audit noise with no offsetting value.
5. **A real browser smoke test**: everything verified so far is unit/feature-level (Pest/PHPUnit hitting routes and Eloquent directly) or a Blade-view render in isolation. Nobody has logged into `/admin` or `/app` in a real browser and clicked through a Filament panel. A Dusk or Pest v4 browser test (or even a manual pass) against the seeded data would catch anything a unit test can't — JS console errors, Livewire wiring issues, broken navigation between the 9 custom pages.
6. **Branch protection**: no evidence was found (nor is it visible from git/CI) that `main` has required-status-checks branch protection configured. Without it, a red PR can still be merged by hand. This is a GitHub repo-settings item, not code — worth checking under Settings → Branches.

### C. Required checks — a concrete pickup checklist

Run these in order; each is a real command with a real pass/fail, not a self-report:

```bash
# 1. Composer — clean-room install + lockfile validity
rm -rf vendor && composer install --prefer-dist --no-interaction
composer validate --strict

# 2. Frontend — clean-room install + build
rm -rf node_modules public/build && pnpm install --frozen-lockfile
pnpm run build   # must produce public/build/manifest.json

# 3. Database — both directions, plus seeding
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed --force

# 4. Style & static analysis — make phpstan actually gate
./vendor/bin/pint --test
./vendor/bin/phpstan --no-progress   # (no `|| true` — read the real output)

# 5. Full test suite
php artisan test

# 6. Gates that are supposed to run for real but currently don't
npx secretlint "**/*"
npx knip
node _qa/cloud-sandbox-independence-gate.mjs
node _qa/tadika-ui-ux-quality-gate.mjs
node _qa/tadika-docs-doctor.mjs
node _qa/tadika-quality-doctor.mjs

# 7. Browser smoke test (manual, or via Dusk/Pest v4 browser testing)
php artisan serve
# then log into /admin and /app and click through each of the 9 custom pages
```

Anything that fails here is real signal — see `errors-fixes.md` ERR-023 through ERR-028 for what this exact checklist already found once (a stale lockfile, a PHP fatal from a property-type mismatch, a duplicate migration, 27 Pint violations, a fully broken test suite, and a missing CI build step). `AGENTS.md` Prime Directive #5 has the rule this checklist exists to enforce: nothing gets called "passed" without actually being run.

## [WP-LOCAL-VERIFICATION-2026-08-23] Codex Pickup Receipt

- Repository: `main` fast-forwarded from `3393e3b` to fetched `origin/main` `a6a38a2`.
- Static gates: all four `_qa/` scripts pass with zero warnings.
- Composer: `composer install --prefer-dist --no-interaction` completed; `composer validate --strict` passes.
- Frontend: pinned pnpm 9.15.9 in `package.json` and CI, kept overrides in the pnpm 9-supported `package.json` location, regenerated the lockfile with pnpm 9; `pnpm install --frozen-lockfile` passes; `pnpm run build` passes and writes `public/build/manifest.json`.
- Tests: `php vendor/bin/pest --compact` passes with 12 tests and 34 assertions. `php artisan test` is not a registered Laravel command in this checkout; Pest is the working runner.
- Formatting: `php vendor/bin/pint --test app config database routes tests` passes. The full vendored `packages/filament-crud-maker` tree still has line-ending/style findings.
- PHPStan: `php vendor/bin/phpstan --no-progress --memory-limit=512M` completes but reports 57 existing errors. CI still runs it advisory-only (`|| true`); do not claim static analysis is green.
- Next action: triage the 57 PHPStan errors, then decide whether the vendored CRUD-maker package should be formatted or excluded from the application Pint scope.
