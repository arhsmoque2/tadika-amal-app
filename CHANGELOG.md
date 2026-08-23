# Changelog

All notable changes to the Tadika Amal Apps project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- **Universal Multi-Module Import, Tri-Format Export & AI-Ready Diagnostic Pipeline (ADR-008)**:
  - `docs/pattern-research.md`: Comprehensive audit and scorecard of 8 candidate repositories (`pxlrbt/filament-excel`, `waadmawlood/filament-import-wizard`, `santwer/Exporter`, `bernskiold/laravel-ppt`, `elgiborsolution/laravel-report-builder`, `codeparl/document-builder`, `saroven/laravel-reportify`, `konnco/filament-import`).
  - `adr/ADR-008`: Formally recorded architectural decisions for Universal Import Wizard, Tri-Format Export (`.xlsx`, `.docx`, `.pptx`, `.pdf`), HTML Posters, and structured JSON reporting for AI chat co-pilot prompts and Sentinel anomaly diagnostics.
  - `UniversalImportExportService.php`: Model-first schema definition contracts for students, teachers, timetables, and fee invoices.
  - `PresentationDeckService.php`: Automated PowerPoint slide deck generation (`.pptx`) for parent orientation briefings, weekly RPH thematic lesson decks, and AGM reports.
  - `WordTemplateExportService.php`: High-fidelity templated `.docx` generation with `${token}` replacement and nested tables for admission offer letters and JKM incident logs.
  - `AiReportDiagnosticService.php`: Dynamic JSON evaluation payload generation for AI Teacher Co-Pilot (qualitative *Ulasan Perkembangan Murid*) and administrative sentinel operational diagnostics.
  - `PosterDocumentService.php`: HTML / Blade poster and certificate rendering engine with PDF export.
  - `resources/views/reports/poster-health-bulletin.blade.php`: Daily morning health bulletin noticeboard poster template.
  - `resources/views/reports/certificate-award.blade.php`: Student Certificate of Achievement / *Sijil Penghargaan* template.
  - `resources/views/reports/orientation-deck-summary.blade.php`: Printable parent orientation briefing summary template.
- **Canonical ARH Documentation Suite**:
  - `INTENT.md`, `SCENARIOS.md`, `CAPABILITIES.md`, `ARCHITECTURE.md`, `DESIGN.md`, `IMPLEMENTATION.md`, `DEVTOOLS.md`, `RECIPES.md`, `QUALITY-GATES.md`, `GAPS.md`, `GOTCHAS.md`, `HANDOFF.md`.
  - `adr/`: ADR-001 through ADR-008.
