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
- [`adr/`](adr/) — 5 Architecture Decision Records:
  - `ADR-001`: Laravel 12 Application Framework.
  - `ADR-002`: Filament v4 Admin Panel (v1 Scoped).
  - `ADR-003`: JSON Schema for Runtime-Configurable Fields.
  - `ADR-004`: Append-Only Assessment Session Records.
  - `ADR-005`: Starter Kit Selection & Scaffolding Accelerators (`filakitv4` base + `filament-crud-maker` generator + `filament-shield`).
  - `ADR-006`: Academico SIS Adaptation for Timetables, Attendance Matrix, Document Pipeline & KSPK Assessment.

### Delivered in Branch `feat/academico-sis-scaffolding`:
- **Database Schemas & Migrations**:
  - `schools`, `teachers`, `rooms`, `cohorts`, `students`
  - `timetable_slots`, `events` (Weekly schedules per class & per teacher)
  - `attendance_records` (Fast daily roll-call: Hadir, Tidak Hadir, Sakit, Cuti)
  - `skills`, `skill_scales`, `skill_evaluations`, `assessment_reports` (Preschool KSPK rubric matrix)
- **Eloquent Models & Tenant Scopes**:
  - `School`, `Teacher`, `Room`, `Cohort`, `Student`, `TimetableSlot`, `Event`, `AttendanceRecord`, `Skill`, `SkillScale`, `SkillEvaluation`, `AssessmentReport`.
- **Document Services Pipeline**:
  - `DocumentTemplateService`: `.docx` dynamic templating via PHPWord `TemplateProcessor` (Offer letters & registration forms).
  - `AssessmentReportPdfService`: Official Malaysian preschool annual progress report card generation via mPDF & Blade.
  - `AttendanceSpreadsheetService`: CSV / Excel cohort attendance export.
- **Filament & Livewire Pages**:
  - `TeacherDashboard`: Assigned class roster, pending attendance warning banner, today's schedule slots, quick action triggers.
  - `CohortAttendance`: Livewire matrix roll-call page with single-click status cycling and "Tanda Semua Hadir".
  - `ClassTimetable`: Visual weekly grid view filtered by Cohort or Teacher.
  - `SkillEvaluationPage`: Full matrix assessment scoring interface against KSPK learning standards.
- **Seeders**:
  - `TadikaAmalKspkSeeder`: Populates sample preschool tenants, teachers, cohorts (5 & 6 Tahun), students, timetable slots, and official KSPK skills.

---

## [WP-MENTAL-MODEL] The Immutable Invariant

> **The platform provides the containers; the school provides the content.**
> Never hardcode curriculum names (*Iqra'*, *Hafazan*, *Solat*, specific subjects) as rigid database columns. They are configured dynamically by the school at runtime using the schema engine.

---

## [WP-NEXT] Next Immediate Action: Review & Merge to Main

1. Review PR / diff on branch `feat/academico-sis-scaffolding`.
2. Run `php artisan migrate --seed` to test full seeder with SQLite/MySQL.
3. Merge `feat/academico-sis-scaffolding` to `main`.
