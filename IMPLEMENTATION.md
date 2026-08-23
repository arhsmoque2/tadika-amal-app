# Implementation

## [IMP-STRATEGY] Phased Delivery Strategy

> The implementation proceeds strictly in verified vertical slices. Each phase is independently testable against its corresponding capabilities and quality gates.

```text
Phase 1: Documents & Platform Tombstone (COMPLETED)
Phase 2: Laravel 12 & Filament v4 Scaffolding + Core Entities (CURRENT TARGET)
Phase 3: Dynamic Schema Engine & Student Profile Builder (CAP-PROFILE-BUILDER, CAP-STUDENT-REGISTRY)
Phase 4: Attendance & Timetable Grid (CAP-ATTENDANCE, CAP-TIMETABLE-BUILDER)
Phase 5: Configurable Assessment & Append-Only History (CAP-ASSESSMENT-BUILDER, CAP-ASSESSMENT-RECORD)
Phase 6: Reporting, Querying & Hardening (CAP-QUERY-REPORT, CAP-ROLES-ACCESS)
```

---

## [IMP-PHASES] Execution Phases in Detail

### Phase 2: Base Scaffolding & Core Relational Data
- Scaffold pristine Laravel 12 project using `composer create-project`.
- Install Filament v4 (`filament/filament:^4.0`), Spatie Permission, and SQLite/PostgreSQL setup.
- Run migrations for `schools`, `users`, `cohorts`, and baseline `students`.
- Seed default Admin & Teacher accounts.

### Phase 3: Dynamic Profile Builder & Student Roster
- Implement `profile_schemas` table and Filament Admin Resource with `Repeater` builder.
- Create dynamic Form Builder helper that compiles JSON schema into Filament components.
- Complete Student Resource: photo upload, basic fields, and dynamically loaded custom fields.

### Phase 4: Attendance Roster & Timetable
- Build `attendances` table and custom Filament Daily Attendance page.
- Implement auto-dated roster with one-click status toggles and absence notes.
- Build `timetables` table and responsive weekly slot manager.

### Phase 5: Dynamic Assessment Engine & Session Recording
- Implement `assessment_schemas` builder.
- Implement `assessment_records` append-only Eloquent model enforcing **ADR-004** invariants.
- Build single/bulk assessment session entry page and historical timeline view.

### Phase 6: Reporting & Quality Gate Verification
- Implement multi-criteria query and report views (date ranges, student drill-down).
- Run full automated test suite (Pest PHP), linting (Pint), and documentation verification.

---

## [IMP-CONTRACT] Boundary-First Rules

1. **No Hardcoded Curriculum**: Zero database seeders or migration columns named after specific subjects or assessment stages.
2. **Append-Only Enforcement**: Any assessment update route must be blocked at model policy level.
3. **Traceability**: All transitions must be recorded with verified receipt in `HANDOFF.md`.
