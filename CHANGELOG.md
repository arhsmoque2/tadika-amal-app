# Changelog

All notable changes to the Tadika Amal Apps project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- **Canonical ARH Documentation Suite**:
  - `INTENT.md`: Platform philosophy ("MS Word / Instagram for Kindergarten Ops", not prescriptive syllabus).
  - `SCENARIOS.md`: 7 actor event flows (Profile, Attendance, Dynamic Assessment, Timetable, Query).
  - `CAPABILITIES.md`: 8 core platform capabilities derived from actor scenarios.
  - `ARCHITECTURE.md`: Technical specification (Laravel 12, Filament v4, multi-tenancy, dynamic schema engine).
  - `DESIGN.md`: Screen contracts for Attendance Sheet, Dynamic Assessment Recorder, and Timetable Grid.
  - `IMPLEMENTATION.md`: 6-phase execution strategy.
  - `DEVTOOLS.md`: Toolchain pinning, PHP/Pint/Pest commands, and ARH devkit harness integration.
  - `RECIPES.md`: Copy-paste operational runbooks.
  - `QUALITY-GATES.md`: Local, documentation, and dynamic schema safety criteria.
  - `GAPS.md`: Parked scope registry (Parent Portal, Billing, WhatsApp alerts in v2).
  - `GOTCHAS.md`: 5 failure capsules (including anti-pattern of hardcoded curriculum).
  - `docs/pattern-research.md`: Pattern analysis of `school.ly`.
  - `adr/`: ADR-001 (Laravel 12), ADR-002 (Filament v4 v1), ADR-003 (JSON Schema for Fields), ADR-004 (Append-Only Records).
