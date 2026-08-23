# Devtools

## [DEV-TOOLCHAIN] Pinned Toolchain & System Standards

> Toolchains are pinned in accordance with ARH OS Standards (`AGENTS.md` §3).

- **PHP**: `8.4.x` (verified active on system: `PHP 8.4.24`)
- **Composer**: `2.x` (`composer.bat`)
- **Node.js**: `^22.x` (managed via `fnm 1.39.0`)
- **Package Manager**: `pnpm 9.15.9` / `pnpm 11.22.0`

---

## [DEV-SETUP] Local & Sandbox Setup

```powershell
# 1. Install PHP dependencies
composer install

# 2. Setup environment (Hermetic fallback defaults to SQLite)
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
# 1. Cloud Sandbox Independence Gate (Zero hardcoded host paths & lockfile freshness)
pnpm run qa:sandbox

# 2. Cloud Infrastructure Quality Gate (Cloud Run, Neon DB pooling, R2 S3 adapter, Secret Manager)
pnpm run qa:infra

# 3. Infrastructure Quality Gate Unit Tests (Fixtures & Negative Regression Tests)
pnpm run test:qa

# 4. Domain-Specific UI/UX Quality Gate (Blade balance, Touch targets >=44px, LHDN/JKM legal text)
pnpm run qa:ui

# 5. ARH Documentation Compliance Doctor
pnpm run docs:check

# 6. Combined Master Automated Quality Gate
pnpm run qa:all

# 7. PHP Code Style & Static Linting (Laravel Pint with PSR-12 and Alpha Imports)
composer pint

# 8. PHP Feature & Unit Test Suite (Hermetic SQLite In-Memory + S3/R2 Disk Mocking)
php artisan test
```

---

## [DEV-SANDBOX] Zero-Ask Cloud Sandbox Agent Protocol

When developing inside ephemeral cloud sandboxes (Claude Code, Codespaces, Gitpod):
1. **Zero Secret Requirement**: Do not attempt to query live GCP WIF, Neon API, or Cloudflare tokens locally.
2. **Hermetic Testing**: The test suite runs against in-memory SQLite and fake storage disks out-of-the-box (17 feature/unit tests, 51 assertions).
3. **Static Pre-Push Verification**: Run `pnpm run qa:all` before pushing. Static audits verify Dockerfile, DB URLs, R2 configs, and CI workflow safety offline without needing cloud CLI tools.
4. **Automated CI/CD**: Real provisioning, branch creation, image build, and deployments execute securely on GitHub Actions CI.

---

## [DEV-PRACTICE] Operational Practice & Error Logging

1. **Continuous Error Harvesting**: Any bug, lint failure, layout collision, or deployment trap must be recorded in [`errors-fixes.md`](errors-fixes.md) before closing a task.
2. **Pre-Commit Verification**: Run `pnpm run qa:all` and `composer pint` before committing changes.
3. **Multi-Tenancy Guard**: Every new migration and Eloquent model must include `school_id` foreign-key isolation.
