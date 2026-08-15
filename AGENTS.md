# AGENTS.md — Tadika Amal Apps

## 1. Capability Route & Cold-Start Discipline

When entering this repository:
1. Read [`README.md`](README.md) and [`PROPOSAL.md`](PROPOSAL.md) to understand current state and decisions.
2. The user will select between **Option A (Filament v4)**, **Option B (Vue 3 / Inertia)**, or **Option C (Hybrid)**.
3. Once the decision is confirmed, initialize the scaffold following standard Laravel 12 + PHP 8.4 + Tailwind CSS v4 patterns.

## 2. Technology & Tooling Standards
- **PHP**: PHP 8.4+ via `composer`.
- **Node.js**: `pnpm` + Vite.
- **Database**: PostgreSQL (Neon) or SQLite in-memory for testing.
- **Code Style**: Laravel Pint (`pint.json`).
- **Tests**: Pest / PHPUnit + Playwright for browser E2E flows.
- **Git Authority**: Private repo on `arhsmoque2/tadika-amal-app`.

## 3. Prime Directives
- **Reuse > Create**: Leverage established packages (`filament`, `saade/filament-fullcalendar`, `hammadzafar05/filament-mobile-preset`, `kstmostofa/laravel-whatsapp`).
- **Verify > Infer**: Always verify rendered UI with real browser tests and Playwright before declaring done.
- **Zero Actions Waste**: Run builds locally or on Google Cloud Build to preserve GitHub Actions quota.
