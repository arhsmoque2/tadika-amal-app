# Tadika Amal Apps

A school operations platform for Malaysian Islamic kindergartens. Replaces paper registers, physical student folders, and scattered Excel sheets with a structured, queryable digital workspace — without prescribing how the school runs its curriculum.

## [README-ROUTE] Repository Route

> Use the documents below to find what you need without reading a monolithic manual.

| Question | Document |
| :--- | :--- |
| Why this exists and what it is not | [`INTENT.md`](INTENT.md) |
| What actors do inside the platform | [`SCENARIOS.md`](SCENARIOS.md) |
| What the platform must be able to do | [`CAPABILITIES.md`](CAPABILITIES.md) |
| Technology stack and system design | [`ARCHITECTURE.md`](ARCHITECTURE.md) *(not yet written — pending stack decision)* |
| Execution plan and phase sequence | [`IMPLEMENTATION.md`](IMPLEMENTATION.md) *(not yet written)* |
| Dev commands and runbooks | [`RECIPES.md`](RECIPES.md) *(not yet written)* |
| What must pass before a phase is done | [`QUALITY-GATES.md`](QUALITY-GATES.md) *(not yet written)* |
| Open questions and parked scope | [`GAPS.md`](GAPS.md) |
| Known failure modes and wrong turns | [`GOTCHAS.md`](GOTCHAS.md) |
| Current state and next action | [`HANDOFF.md`](HANDOFF.md) |
| Prior stack comparison (reference) | [`PROPOSAL.md`](PROPOSAL.md) |

## [README-MENTAL-MODEL] One Sentence

> The platform provides the containers. The school provides the content.

Like Microsoft Word, not a pre-typed PDF. Like Instagram, not a content brief. The platform does not know what subjects a school teaches or how it assesses students. It provides the tools — profile builder, timetable builder, attendance register, assessment recorder — that the school fills with its own names, fields, and logic.

## [README-STATUS] Current Status

| Concern | State | Evidence |
| :--- | :--- | :--- |
| Product intent and boundary | DOCUMENTED | `INTENT.md` |
| Actor scenarios (v1 teacher scope) | DOCUMENTED | `SCENARIOS.md` — 7 scenarios |
| Platform capabilities (v1) | DOCUMENTED | `CAPABILITIES.md` — 8 capabilities |
| Open questions | DOCUMENTED | `GAPS.md` — 8 items |
| Known failure modes | DOCUMENTED | `GOTCHAS.md` — 5 capsules |
| Stack decision | NOT YET MADE | G-001 in `GAPS.md` |
| Application code | NOT STARTED | Pending stack decision |
| Database schema | NOT STARTED | Pending stack decision |
| UI / Filament panel | NOT STARTED | Pending stack decision |

## [README-SCOPE] v1 Scope — Teacher Module

The first shippable version is scoped to the **teacher actor** only. No parent portal. No billing. No WhatsApp.

A teacher in v1 can:
- Define a student profile structure for their class
- Register students with the school's chosen fields
- Take daily attendance with a checkbox per student
- Define their own assessment areas and fields
- Record student assessment sessions that accumulate over time
- Query any student's attendance or progress across any date range
- Build and view their class timetable, auto-synced to today

## [README-START] Start Here (After Stack Decision)

```powershell
# After ARCHITECTURE.md is written and stack is confirmed:
# See RECIPES.md for install and dev server commands.
```
