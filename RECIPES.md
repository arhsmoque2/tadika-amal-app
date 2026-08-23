# Recipes

## [RECIPE-NEW-INSTALL] Fresh Environment Setup

```powershell
# In repository root
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

---

## [RECIPE-SERVE] Start Local Services

```powershell
# Start local web development server (default port 8000)
php artisan serve
```

---

## [RECIPE-ADMIN-USER] Create Admin / Teacher User

```powershell
# Create Filament Superadmin / School Admin
php artisan make:filament-user
```

---

## [RECIPE-SCHEMA-REFRESH] Clean Database Reset

```powershell
# Caution: Resets all local records
php artisan migrate:fresh --seed
```

---

## [RECIPE-TEST] Run Pest / PHPUnit Tests

```powershell
# Run the full automated verification test suite (Hermetic SQLite + S3 Fake)
php artisan test --parallel
```

---

## [RECIPE-INFRA-QA] Run Infrastructure Quality Gates & Regression Fixtures

```powershell
# 1. Run static infrastructure config & deployment security gate
node _qa/tadika-infra-quality-gate.mjs

# 2. Run gate unit tests & negative regression fixtures
node --test _qa/tests/tadika-infra-quality-gate.test.mjs

# 3. Run complete automated master quality gate
pnpm run qa:all
```
