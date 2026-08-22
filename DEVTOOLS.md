# Devtools

## [DEV-TOOLCHAIN] Pinned Toolchain & Prerequisites

> Ensure standard ARH-pinned toolchains are active before running commands.

- **PHP**: `^8.2` (managed via system or local toolchain)
- **Composer**: `^2.7`
- **Node.js**: `^22.x` (managed via `fnm`)
- **Package Manager**: `pnpm` / `npm`

---

## [DEV-SETUP] Local Setup

```powershell
# 1. Install PHP dependencies
composer install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Migrate and seed base schema
php artisan migrate:fresh --seed

# 4. Install & build frontend assets
npm install
npm run build
```

---

## [DEV-SERVER] Local Development Server

```powershell
# Run the Laravel dev server
php artisan serve

# Run Vite asset compiler (for customized styles)
npm run dev
```

---

## [DEV-CHECK] Quality & Test Checks

```powershell
# Run static analysis and linting
vendor/bin/pint --test

# Run Pest test suite
php artisan test
```
