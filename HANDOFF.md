# Handoff

## [WP-STATE] Current State

> Phase 1 (Docs Overhaul & Architecture Alignment), Phase 2 (Base SIS & Compliance Scaffolding), and Phase 3 (Universal Import/Export, Multi-Format Document Generation & AI JSON Pipeline per ADR-008) are fully implemented and verified against the ARH Quality Gate.

### Delivered Document & Architectural Suite:
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
- [`docs/pattern-research.md`](docs/pattern-research.md) — In-depth audit and scorecard across 8 candidate repositories.
- [`adr/`](adr/) — 8 Architecture Decision Records:
  - `ADR-001`: Laravel 12 Application Framework.
  - `ADR-002`: Filament v4 Admin Panel (v1 Scoped).
  - `ADR-003`: JSON Schema for Runtime-Configurable Fields.
  - `ADR-004`: Append-Only Assessment Session Records.
  - `ADR-005`: Starter Kit Selection & Scaffolding Accelerators (`filakitv4` + `filament-crud-maker` + `filament-shield`).
  - `ADR-006`: Academico SIS Adaptation for Timetables, Attendance Matrix, Document Pipeline & KSPK Assessment.
  - `ADR-007`: Preschool Operational Extensions & Regulatory Compliance Suite (WhatsApp Broadcaster, Saringan Pagi, RPH Planner, LHDN Invoicing, JKM Incident Log).
  - `ADR-008`: Universal Multi-Module Import, Tri-Format Export, Presentation Generator, and AI-Ready Diagnostic Pipeline.

### Delivered Services & Reporting Pipelines:
1. **Universal Import & Export**:
   - `UniversalImportExportService.php`: Model-first schema definition contracts for students, teachers, timetables, and fee invoices.
   - `AttendanceSpreadsheetService.php`: CSV / Excel cohort attendance matrix export.
2. **Templated Word (`.docx`) Documents**:
   - `WordTemplateExportService.php`: Dynamic `${token}` replacement and nested tables for admission offer letters and JKM incident logs.
   - `DocumentTemplateService.php`: Student registration forms & admission documents.
3. **PowerPoint Presentations (`.pptx`)**:
   - `PresentationDeckService.php`: Automated slide deck generation for parent orientation briefings, weekly RPH thematic lesson decks, and AGM reports.
4. **PDF Reports & Visual Posters**:
   - `AssessmentReportPdfService.php`: Official annual KSPK developmental progress report cards via mPDF & Blade.
   - `FeeReceiptPdfService.php`: LHDN Section 46(1)(r) statutory preschool tax relief fee receipts.
   - `PosterDocumentService.php`: Daily morning health bulletin noticeboard posters, student certificates of achievement, and event flyers.
5. **AI Chat & Diagnostic Bridge**:
   - `AiReportDiagnosticService.php`: Structured JSON evaluation payload generator for AI Teacher Co-Pilot (qualitative *Ulasan Perkembangan Murid*) and administrative sentinel operational diagnostics.

---

## [WP-MENTAL-MODEL] The Immutable Invariant

> **The platform provides the containers; the school provides the content.**
> Never hardcode curriculum names (*Iqra'*, *Hafazan*, *Solat*, specific subjects) as rigid database columns. They are configured dynamically by the school at runtime using the schema engine.

---

## [WP-NEXT] Next Immediate Action

1. Verify all UI/UX templates and services via `node _qa/tadika-ui-ux-quality-gate.mjs`.
2. Commit feature branch `feat/universal-import-export-and-doc-engine` and merge to `main`.
