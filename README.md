# Tadika Amal Apps

A modular, extensible school operations platform for Malaysian Islamic kindergartens and preschools. Replaces paper registers, physical student cards, and disjointed spreadsheets with a structured, queryable digital workspace.

> 🚀 **Live Production Deployment**: [https://tadika-amal-app-gmnvf7efyq-as.a.run.app](https://tadika-amal-app-gmnvf7efyq-as.a.run.app)  
> 🟢 **Live Health Check**: [https://tadika-amal-app-gmnvf7efyq-as.a.run.app/up](https://tadika-amal-app-gmnvf7efyq-as.a.run.app/up) (`200 OK`) | 🛡️ **Admin Portal**: [https://tadika-amal-app-gmnvf7efyq-as.a.run.app/admin/login](https://tadika-amal-app-gmnvf7efyq-as.a.run.app/admin/login) | 👩‍🏫 **Staff Workspace**: [https://tadika-amal-app-gmnvf7efyq-as.a.run.app/app/login](https://tadika-amal-app-gmnvf7efyq-as.a.run.app/app/login)

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
| **Decisions** | [`adr/`](adr/) | Architectural Decision Records (ADR-001 to ADR-008) |
| **Research** | [`docs/pattern-research.md`](docs/pattern-research.md) | In-depth audit of 8 candidate repositories & evaluation matrix |
| **Risks & Antipatterns**| [`GOTCHAS.md`](GOTCHAS.md) | Failure capsules & permanent fixes |
| **Quality & Error Log**| [`errors-fixes.md`](errors-fixes.md) | Quality checks, scripts & remediation register |
| **Parked Scope** | [`GAPS.md`](GAPS.md) | Open questions & deferred items (Parent Portal in v2) |
| **Continuation** | [`HANDOFF.md`](HANDOFF.md) | Current verified state & next action |

---

## [README-DEPLOYMENT] Live Production Deployment

| Service | Endpoint | Status |
| :--- | :--- | :--- |
| **Main Web Portal** | [https://tadika-amal-app-gmnvf7efyq-as.a.run.app](https://tadika-amal-app-gmnvf7efyq-as.a.run.app) | `200 OK` (Live) |
| **Health Probe** | [https://tadika-amal-app-gmnvf7efyq-as.a.run.app/up](https://tadika-amal-app-gmnvf7efyq-as.a.run.app/up) | `200 OK` (Live) |
| **Admin Operations Panel** | [https://tadika-amal-app-gmnvf7efyq-as.a.run.app/admin/login](https://tadika-amal-app-gmnvf7efyq-as.a.run.app/admin/login) | `200 OK` (Live) |
| **Teacher & Staff Workspace** | [https://tadika-amal-app-gmnvf7efyq-as.a.run.app/app/login](https://tadika-amal-app-gmnvf7efyq-as.a.run.app/app/login) | `200 OK` (Live) |

- **Compute & Runtime**: Google Cloud Run (`asia-southeast1`) via FrankenPHP 8.4 container image.
- **Relational Database**: Neon Serverless PostgreSQL with auto-scaling connection pooling.
- **Background Cron**: Google Cloud Scheduler triggering `php artisan schedule:run` every minute.

---

## [README-STATUS] Current Status

| Concern | State | Evidence |
| :--- | :--- | :--- |
| **Docs & Platform Tombstone** | IMPLEMENTED | Full ARH documentation suite complete |
| **ADR Suite (ADR 001 - 011)** | ACCEPTED | `adr/` directory populated with ADR-001 through ADR-011 |
| **Base Scaffolding & Core SIS**| IMPLEMENTED | 110+ models, migrations & Livewire workspace pages |
| **Multi-Doc & Presentation Pipeline** | IMPLEMENTED | `.xlsx`, `.docx`, `.pptx`, `.pdf`, and HTML poster services active |
| **AI JSON Diagnostic Bridge** | IMPLEMENTED | Teacher Co-Pilot prompt generator & Sentinel health snapshots |
| **UI/UX & Regulatory Quality Gate** | VERIFIED | `_qa/tadika-ui-ux-quality-gate.mjs` passing 100% (0 warnings) |
| **Production Cloud Run & Neon** | LIVE & ACTIVE | Cloud Run revision deployed with Secret Manager references |

---

## [README-START] Quick Start

```powershell
# Read architecture and setup recipes
Get-Content ARCHITECTURE.md
Get-Content RECIPES.md

# Run local quality doctors & pre-push gates
pnpm run qa:all
```
