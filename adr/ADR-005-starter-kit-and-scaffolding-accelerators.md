# ADR-005: Starter Kit Selection & Scaffolding Accelerators

**Status:** Accepted

## [ADR-005-CONTEXT] Context

> Rather than building boilerplate authentication, panels, and CRUD controllers from scratch, four candidate repositories were cloned and audited to extract battle-tested scaffolding, role management, audit logging, and code generation capabilities:
> 1. `jeffersongoncalves/filakitv4`
> 2. `AqibUllah/Laravel-Filament-Starter-Kit`
> 3. `felipereisdev/filament-crud-maker`
> 4. `mhmdhussein/school.ly`

The objective was to accelerate delivery of Phase 2 while preserving Tadika Amal's core invariant: **the platform provides containers, the school provides content** (no hardcoded curriculum tables, dynamic JSON schemas per **ADR-003**).

## [ADR-005-DECISION] Decision

1. **Adopt `filakitv4` as Base Foundation**:
   - Establish the Filament v4 multi-panel hierarchy (`AdminPanelProvider`, `AppPanelProvider` for teachers, `GuestPanelProvider`).
   - Standardize on decoupled `Schemas/` and `Tables/` component directories (Filament v4 standard).
   - Bundle `filament-pwa` to fulfill low-connectivity classroom requirements (**G-008**).
   - Incorporate `filament-developer-logins` and Larastan/Pint/Pest toolchains.

2. **Incorporate Specific Modules from `Laravel-Filament-Starter-Kit`**:
   - `bezhansalleh/filament-shield:^4.0` for visual Role & Permission governance.
   - `pxlrbt/filament-activity-log` and `spatie/laravel-activitylog` for immutable audit logging (**ADR-004**).
   - `pxlrbt/filament-excel` for automated tabular exports.

3. **Adopt `filament-crud-maker` as Developer Scaffolding Tool**:
   - Use `php artisan make:filament-crud` for generating baseline Eloquent models, migrations, and decoupled Filament v4 Schemas/Tables.

4. **Park Full-Stack Starter Incompatibilities**:
   - Do not import `school.ly`'s Inertia.js/React frontend or rigid relational columns into v1.

## [ADR-005-CONSEQUENCES] Consequences

- Time-to-functional prototype reduced by ~80% through pre-configured panels, authentication, and generator scripts.
- Multi-tenancy scoping and role-based policies are pre-wired.
- Code generation conforms immediately to Filament v4 decoupled Form/Table architecture.
- PWA offline caching infrastructure is available from Day 1.
