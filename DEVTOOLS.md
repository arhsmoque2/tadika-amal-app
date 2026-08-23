# Devtools

## [DEV-TOOLCHAIN] Pinned Toolchain & System Standards

> Toolchains are pinned in accordance with ARH OS Standards (`AGENTS.md` §3).

- **PHP**: `8.4.x` (verified active on system: `PHP 8.4.24`)
- **Composer**: `2.x` (`C:\Users\Abdul Rahman Hilmi\AppData\Local\Programs\composer\composer.bat`)
- **Node.js**: `^22.x` (managed via `fnm 1.39.0`)
- **Package Manager**: `pnpm 11.22.0` / `npm`

---

## [DEV-SETUP] Local Setup

```powershell
# 1. Install PHP dependencies
composer install

# 2. Setup environment
copy .env.example .env
php artisan key:generate

# 3. Migrate and seed base schema
php artisan migrate:fresh --seed

# 4. Install & build frontend assets
pnpm install
pnpm run build
```

---

## [DEV-SERVER] Local Development Server

```powershell
# Run the Laravel dev server (default port 8000)
php artisan serve

# Run Vite asset compiler (for customized styles / Livewire assets)
pnpm run dev
```

---

## [DEV-CHECK] Quality & Verification Gates

```powershell
# 1. Domain-Specific UI/UX Quality Gate (Blade balance, Touch targets >=44px, LHDN/JKM legal text)
pnpm run qa:ui

# 2. Master ARH JS Doctor (Oxlint, Secret Scanner, Schema Validator, Layout/A11y, Ratchet, Docs)
pnpm doctor

# 3. Combined Automated Quality Gate
pnpm run qa:all

# 4. Recursive PHP Syntax Linting (PHP 8.4)
Get-ChildItem -Path app,database -Filter *.php -Recurse | ForEach-Object { $res = php -l $_.FullName 2>&1; if ($res -notmatch "No syntax errors detected") { Write-Host $res } }

# 5. PHP Code Style & Static Linting (Laravel Pint with PSR-12 and Alpha Imports)
composer pint

# 6. PHP Static Analysis (Larastan Level 5)
composer phpstan

# 7. Dead Code & Orphan Dependency Scanner (Knip)
npx knip

# 8. PWA & Lighthouse Accessibility / Performance Assertions
npx lighthouserc
```

---

## [DEV-PRACTICE] Operational Practice & Error Logging

1. **Continuous Error Harvesting**: Any bug, lint failure, layout collision, or deployment trap must be recorded in [`errors-fixes.md`](errors-fixes.md) before closing a task.
2. **Pre-Commit Verification**: Run `pnpm run qa:all` and `composer pint` before committing changes.
3. **Multi-Tenancy Guard**: Every new migration and Eloquent model must include `school_id` foreign-key isolation.

---

## [DEV-PERF] Performance Profiling & Benchmark Verifier

```powershell
# Run Median-of-Trials performance speedup benchmark
node -e "import('D:/_ARH-AGENT-OS/_AGENT-CAPABILITIES/arh-js-devkit/lib/benchmark-verify.mjs').then(m => console.log('Benchmark harness loaded.'))"
```
