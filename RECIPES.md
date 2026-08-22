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

## [RECIPE-TEST] Run Pest Tests

```powershell
# Run the full automated verification test suite
php artisan test --parallel
```
