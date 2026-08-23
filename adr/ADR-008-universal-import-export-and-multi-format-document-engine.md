# ADR-008: Universal Multi-Module Import, Tri-Format Export, Presentation Generator, and AI-Ready Diagnostic Pipeline

## Status
**Accepted**

## Date
2026-08-23

---

## Context

During initial onboarding and operational deployment of Tadika Amal Apps, kindergartens transition from heterogeneous data formats (Excel spreadsheets, physical paper registers, and scattered Word templates). 

To ensure seamless adoption and zero data-loss during this transition period, the system requires:
1. **Universal Migration / Import Engine**: Interactive column mapping, relationship linking (e.g. associating students to cohorts by class name), and upsert de-duplication across all operational models (Students, Teachers, Timetables, Fee Invoices).
2. **Universal Tabular Export**: Direct one-click and bulk export of any Filament table to `.xlsx` and `.csv`.
3. **High-Fidelity Document Generation**:
   - **Word (`.docx`)**: Templated document generation with dynamic token replacement, nested tables, and signature blocks.
   - **PowerPoint (`.pptx`)**: Programmatic slide deck generation for parent orientation briefings, weekly RPH thematic lesson decks, and AGM reports.
   - **PDF & HTML Posters**: High-density progress report cards, LHDN tax relief receipts, printable certificates, and morning health bulletins.
4. **AI & Agent Diagnostic Bridge**: A structured JSON reporting pipeline capable of serializing domain metrics and evaluations into clean LLM prompts for teacher remarks co-pilots and administrative anomaly detection.

---

## Candidate Repositories & Evaluation

| Candidate Repository | Evaluated Focus | Compatibility | Decision |
| :--- | :--- | :---: | :--- |
| `waadmawlood/filament-import-wizard` | 4-step interactive column mapping & upsert wizard | ✅ Filament v4 (`^4.0|^5.0`) | **Adopted** for universal transition imports |
| `pxlrbt/filament-excel` | Automated table/form Excel & CSV exports | ✅ Filament v4 (`filament/tables: ^4.0`) | **Adopted** for universal tabular exports |
| `konnco/filament-import` | Outdated import action | ❌ Filament v3 only | **Rejected** (incompatible with Laravel 12/13) |
| `santwer/Exporter` | PHPWord template variable and table replacement | ✅ Laravel 10-13, PHP 8.3+ | **Adopted** for `.docx` formal letters & contracts |
| `bernskiold/laravel-ppt` | Fluent presentation builder over PHPPresentation | ✅ Laravel 11-13, PHP 8.2+ | **Adopted** for `.pptx` orientation & lesson decks |
| `codeparl/document-builder` | Unified pipeline & QR code execution plan | ✅ Laravel 11-12 | **Adopted** for QR gate passes & poster generation |
| `elgiborsolution/laravel-report-builder` | Metadata query engine, HTML poster & JSON output | ✅ Laravel 10-13, PHP 8.2+ | **Adopted** for dynamic reporting & AI JSON bridge |

---

## Decisions

### 1. Universal Import Wizard across Core Resources
All core administrative and operational models (`Student`, `Teacher`, `TimetableSlot`, `FeeInvoice`, `Cohort`) expose an `ImportWizardAction` configured with:
- Model-first reverse mapping dropdowns.
- `BelongsTo` relationship lookups (e.g. matching `cohort_id` via class name strings).
- Deterministic upsert keys (`mykid`, `ic_number`, `staff_no`, `invoice_no`).
- Pre-import schema validation and downloadable CSV of rejected rows.

### 2. Tri-Format Export Strategy
- **Spreadsheets (`.xlsx`, `.csv`)**: Handled via `pxlrbt/filament-excel` attached as table header and bulk actions across all resources.
- **Documents (`.docx`)**: Handled via `WordTemplateExportService` (extending `phpoffice/phpword` / `santwer/Exporter`) merging `${tokens}`, tables, and stamps into master templates.
- **Presentations (`.pptx`)**: Handled via `PresentationDeckService` (extending `bernskioldmedia/laravel-ppt`) generating slide decks with brand themes, agendas, cards, and charts.

### 3. Visual Poster & Certificate Pipeline (HTML / Blade + mPDF)
- Handled via `PosterDocumentService` rendering high-impact Blade poster layouts (certificates, health bulletins) with Tailwind CSS styling and automated QR code passes for parent pickup verification.

### 4. Dynamic Reporting & AI Chat / Sentinel Diagnostic Pipeline
- Handled via `AiReportDiagnosticService` emitting structured JSON payloads:
  - **Teacher Remark Co-Pilot**: Converts student KSPK evaluation records into structured context prompts for generative LLMs to draft personalized qualitative remarks (*Ulasan Perkembangan Murid*).
  - **Administrative Sentinel**: Serializes weekly attendance percentages, health screenings, and fee collection summaries for automated anomaly detection.

---

## Consequences & Invariants

1. **Zero Hardcoded Content**: Templates, slide decks, and import mapping definitions remain dynamic containers; specific kindergarten branding, fees, and themes are injected at runtime.
2. **Tenant Scoping Invariant**: Every import and export job strictly enforces `school_id` tenant scoping across queue workers.
3. **Quality & Mobile Touch Gate**: All interactive modals, export buttons, and poster views must pass the automated UI/UX quality gate (`_qa/tadika-ui-ux-quality-gate.mjs`).
