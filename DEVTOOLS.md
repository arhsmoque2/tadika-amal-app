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
# 1. PHP Code Style & Static Linting (Laravel Pint)
vendor/bin/pint --test

# 2. Automated Test Suite (Pest PHP)
php artisan test

# 3. ARH Standard JavaScript, Layout Integrity & Documentation Doctor
node D:/_ARH-AGENT-OS/_AGENT-CAPABILITIES/arh-js-devkit/bin/arh-js-doctor.mjs .
```
