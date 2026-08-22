# Handoff

## [WP-STATE] Current State

> Phase 1 (Docs Overhaul & Architecture Alignment) is 100% complete and fully tombstoned. The entire canonical ARH documentation suite has been authored and verified.

### Delivered Document Suite:
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
- [`docs/pattern-research.md`](docs/pattern-research.md) — Research notes and adoptable patterns from `school.ly`.
- [`adr/`](adr/) — 4 Architecture Decision Records:
  - `ADR-001`: Laravel 12 Application Framework.
  - `ADR-002`: Filament v4 Admin Panel (v1 Scoped).
  - `ADR-003`: JSON Schema for Runtime-Configurable Fields.
  - `ADR-004`: Append-Only Assessment Session Records.

---

## [WP-MENTAL-MODEL] The Immutable Invariant

> **The platform provides the containers; the school provides the content.**
> Never hardcode curriculum names (*Iqra'*, *Hafazan*, *Solat*, specific subjects) as rigid database columns. They are configured dynamically by the school at runtime using the schema engine.

---

## [WP-NEXT] Next Immediate Action: Phase 2 Base Scaffolding

1. Scaffold fresh Laravel 12 base in `tadika-amal-app`.
2. Install Filament v4 (`filament/filament:^4.0`) and Spatie Permissions.
3. Migrate foundational multi-tenant and authentication tables (`schools`, `users`, `cohorts`, `students`).
4. Validate baseline quality gate (`php artisan test`).
