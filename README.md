# Tadika Amal Apps

A modular, extensible school operations platform for Malaysian Islamic kindergartens and preschools. Replaces paper registers, physical student cards, and disjointed spreadsheets with a structured, queryable digital workspace.

## [README-ROUTE] Repository Route

> Use the canonical documents below to navigate project decisions without reading a monolithic manual.

| Area | Canonical Document | Purpose |
| :--- | :--- | :--- |
| **Why & Boundary** | [`INTENT.md`](INTENT.md) | Product purpose, primary actors, decision principles |
| **User Flows** | [`SCENARIOS.md`](SCENARIOS.md) | 7 actor event flows (Profile, Attendance, Assessment, Timetable) |
| **Requirements** | [`CAPABILITIES.md`](CAPABILITIES.md) | 8 platform capabilities derived from scenarios |
| **Architecture** | [`ARCHITECTURE.md`](ARCHITECTURE.md) | Process model, multi-tenancy, dynamic schema engine, security |
| **UI & Screens** | [`DESIGN.md`](DESIGN.md) | Screen contracts for Attendance, Assessment, Timetable |
| **Roadmap** | [`IMPLEMENTATION.md`](IMPLEMENTATION.md) | Phased delivery strategy (Phase 1 to Phase 6) |
| **Tooling** | [`DEVTOOLS.md`](DEVTOOLS.md) | Pinned toolchains and developer commands |
| **Runbooks** | [`RECIPES.md`](RECIPES.md) | Tested copy-paste setup, test, and seeding recipes |
| **Gates** | [`QUALITY-GATES.md`](QUALITY-GATES.md) | Quality thresholds before feature transition |
| **Decisions** | [`adr/`](adr/) | Architectural Decision Records (ADR-001 to ADR-004) |
| **Research** | [`docs/pattern-research.md`](docs/pattern-research.md) | Insights & divergences from `school.ly` reference |
| **Risks & Antipatterns**| [`GOTCHAS.md`](GOTCHAS.md) | Failure capsules & permanent fixes |
| **Parked Scope** | [`GAPS.md`](GAPS.md) | Open questions & deferred items (Parent Portal in v2) |
| **Continuation** | [`HANDOFF.md`](HANDOFF.md) | Current verified state & next action |

---

## [README-STATUS] Current Status

| Concern | State | Evidence |
| :--- | :--- | :--- |
| **Docs & Platform Tombstone** | IMPLEMENTED | Full ARH documentation suite complete |
| **ADR Suite (ADR 001 - 004)** | ACCEPTED | `adr/` directory populated |
| **Phase 1 Acceptance** | COMPLETE | Aligned with platform-first mental model |
| **Base Scaffolding (Laravel 12 + Filament v4)**| PENDING | Phase 2 target |
| **Dynamic Schema Engine** | DESIGNED | Ready for implementation in Phase 3 |

---

## [README-START] Quick Start

```powershell
# Read architecture and setup recipes
Get-Content ARCHITECTURE.md
Get-Content RECIPES.md
```
