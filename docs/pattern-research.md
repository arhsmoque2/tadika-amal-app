# Pattern Research & Codebase Evaluation

> Comprehensive audit of candidate repositories and reference implementations evaluated to accelerate Tadika Amal Apps without building from scratch.

---

## [RES-1] Candidate Repositories Matrix

| Repository | Source URL | Stack & Tools | Evaluated Role | Decision |
| :--- | :--- | :--- | :--- | :--- |
| **`jeffersongoncalves/filakitv4`** | `https://github.com/jeffersongoncalves/filakitv4.git` | Laravel 13 / Filament v4.5 / Livewire 3.7 / TailwindCSS v4 | **Base Skeleton** | **Adopted as Base Foundation** (Decoupled Schemas/Tables, Multi-Panel, PWA, Dev Logins). |
| **`AqibUllah/Laravel-Filament-Starter-Kit`** | `https://github.com/AqibUllah/Laravel-Filament-Starter-Kit.git` | Laravel 12 / Filament v4 / Shield / ActivityLog / Excel | **Feature Reference** | **Adopted Selected Capabilities** (Filament Shield RBAC, ActivityLog audit trail for ADR-004, Excel export). |
| **`felipereisdev/filament-crud-maker`** | `https://github.com/felipereisdev/filament-crud-maker.git` | Laravel 11-13 / Filament v4-v5 Generator Tool | **CLI Tooling & Accelerator** | **Adopted as Scaffolding Engine** (`make:filament-crud` for zero-boilerplate schema generation). |
| **`mhmdhussein/school.ly`** | `https://github.com/mhmdhussein/school.ly` | Laravel 12 / Fortify / Inertia v2 / React 19 / TypeScript | **Architecture Reference** | **Adopted Architectural Patterns** (Multi-tenant scoping, Fortify 2FA flows). Frontend parked for v2 Parent Portal. |
| **`academico-sis/academico`** | `https://github.com/academico-sis/academico.git` | Laravel 12 / Filament v5 / mPDF / PHPWord / Maatwebsite Excel | **Domain Scaffolding & Engine** | **Adopted Core SIS Modules** (Timetable Grid, Student Attendance Matrix, Teacher Dashboard, Skill Assessment Engine, DOCX/PDF Report Card Pipeline). |
| **`pxlrbt/filament-excel`** | `https://github.com/pxlrbt/filament-excel.git` | Filament Tables v4 & v5 / PhpSpreadsheet / Laravel Excel | **Tabular Data Export** | **Adopted for Universal Excel/CSV Export** (`ExportAction` & `ExportBulkAction` on all Filament resources). |
| **`konnco/filament-import`** | `https://github.com/konnco/filament-import.git` | Filament v3 / Livewire 3 | **Spreadsheet Importer** | **Rejected / Incompatible** (Locked to Filament v3; lacks modern column-mapping & queue batching). |
| **`waadmawlood/filament-import-wizard`** | `https://github.com/waadmawlood/filament-import-wizard.git` | Filament v4-v5 / PhpSpreadsheet 5 / League CSV / Livewire | **Migration & Transition Importer** | **Adopted for Universal Import** (4-step visual wizard, snake_case auto-mapping, `BelongsTo` linking, upsert merge). |
| **`codeparl/document-builder`** *(UnnovateBrains)* | `https://github.com/codeparl/document-builder.git` | Laravel 11-12 / mPDF / PhpSpreadsheet / Barcode / QR | **Unified Document Pipeline** | **Adopted Pipeline Design Patterns** (Driver-based document execution plan, QR code generation for pickup gates). |
| **`santwer/Exporter`** | `https://github.com/santwer/Exporter.git` | Laravel 10-13 / PHPWord 1.4 / LibreOffice | **Templated Word Document Generator** | **Adopted for DOCX Templating** (Dynamic `${token}` replacement, multi-row table injection, badge/seal embedding). |
| **`bernskiold/laravel-ppt`** | `https://github.com/bernskiold/laravel-ppt.git` | Laravel 11-13 / PHPPresentation 1.2 / Spatie Tools | **PowerPoint Presentation Deck Engine** | **Adopted for Slide Decks** (Auto-generating kindergarten orientation decks, weekly RPH lesson slides, AGM reports). |
| **`saroven/laravel-reportify`** | `https://github.com/saroven/laravel-reportify.git` | Laravel 9-13 / mPDF / Laravel Excel | **Multi-Format Reporting** | **Evaluated / Reference Only** (Redundant with `document-builder` and `filament-excel`). |
| **`elgiborsolution/laravel-report-builder`** | `https://github.com/elgiborsolution/laravel-report-builder.git` | Laravel 10-13 / Expression Language / DomPDF / Browsershot | **Dynamic Reporting + AI JSON Bridge** | **Adopted for Dynamic Reports & AI Chat Bridge** (HTML poster rendering + structured JSON output for LLM co-pilot). |

---

## [RES-2] Detailed Repository Assessments

### 1. `jeffersongoncalves/filakitv4`
* **Source**: `https://github.com/jeffersongoncalves/filakitv4.git`
* **Key Assets Adopted**: Decoupled `Schemas/` and `Tables/` directory structure, multi-panel routing (`AdminPanelProvider`, `AppPanelProvider`), and PWA shell caching (`jeffersongoncalves/filament-pwa`).

### 2. `waadmawlood/filament-import-wizard`
* **Source**: `https://github.com/waadmawlood/filament-import-wizard.git`
* **Key Assets Adopted**:
  - **4-Step Wizard Modal**: Upload -> Column Mapping -> Review & Schema Validation -> Background Queue Import.
  - **Reverse Mapping UI**: Shows model fields first with CSV dropdowns, preventing operator confusion.
  - **BelongsTo Relationship Resolution**: Matches related records on the fly (e.g. resolves `cohort_id` by matching class name `"6 Tahun Al-Farabi"`).
  - **Upsert Matching**: Configurable match keys (e.g. `mykid`, `ic_number`, `staff_no`) to update existing records without creating duplicates.
  - **Validation & Rejection CSV**: Previews first 100 rows and outputs a downloadable rejection CSV for invalid rows.

### 3. `pxlrbt/filament-excel`
* **Source**: `https://github.com/pxlrbt/filament-excel.git`
* **Key Assets Adopted**:
  - **Zero-Boilerplate Export**: Direct reflection of Filament Table columns into `.xlsx` and `.csv`.
  - **Bulk & Header Actions**: Attached across `StudentResource`, `TeacherResource`, `FeeInvoiceResource`, and `CohortAttendance`.
  - **Background Queuing**: Heavy multi-cohort export jobs run in workers with automatic signed download notifications.

### 4. `santwer/Exporter`
* **Source**: `https://github.com/santwer/Exporter.git`
* **Key Assets Adopted**:
  - **Token Replacement**: Replaces `${student_name}`, `${mykid}`, `${guardian_name}`, `${fee_amount}` inside `.docx` templates.
  - **Table Injection (WithTables)**: Injects multi-row attendance logs, fee breakdown tables, and timetable schedules into Word files.
  - **Use Cases**: Official Admission Offer Letters (*Surat Tawaran Kemasukan*), JKM Statutory Incident Dossiers, Parent-School Contracts.

### 5. `bernskiold/laravel-ppt`
* **Source**: `https://github.com/bernskiold/laravel-ppt.git`
* **Key Assets Adopted**:
  - **Fluent Slide Deck Builder**: Slide masters, brand palettes (Emerald/Slate), typography, bullet boxes, multi-column layouts, charts.
  - **Use Cases**:
    - **Parent Orientation Deck**: Auto-generated briefing presentation with class rosters, teacher introductions, and school rules.
    - **Weekly RPH Thematic Lesson Deck**: Auto-generated classroom presentation based on the active KSPK weekly theme.
    - **Year-End AGM Presentation**: Enrolment statistics, attendance percentages, and financial summaries.

### 6. `elgiborsolution/laravel-report-builder`
* **Source**: `https://github.com/elgiborsolution/laravel-report-builder.git`
* **Key Assets Adopted**:
  - **Metadata-Driven Query Engine**: Definitions, parameters, aggregates, formulas, and conditional styling.
  - **HTML Poster / Certificate Generator**: Blade layout engine producing visual achievement certificates (*Sijil Penghargaan*) and health bulletins.
  - **Structured JSON Output (JsonRenderer)**: Emits structured metadata, rows, aggregates, and formulas.
  - **AI Chat & Sentinel Bridge**: Feeds structured JSON into AI chat prompts (Gemini/Claude) for:
    1. *Teacher Co-Pilot*: Auto-drafting warm, qualitative KSPK progress remarks (*Ulasan Perkembangan Murid*).
    2. *Administrative Sentinel*: Detecting cohort attendance anomalies and fee arrears patterns.

---

## [RES-3] Conformance & Architectural Invariants

1. [x] All candidate repositories cloned and audited in `_AGENT-WORKSPACE/lab-test-scratchpad/`.
2. [x] Incompatible / legacy packages flagged (`konnco/filament-import` rejected for Filament v4).
3. [x] Tri-format export + presentation decks + AI JSON reporting unified into formal architectural contracts (**ADR-008**).
4. [x] Preserved core invariant: The platform provides the containers and pipelines; the school configures the content.
