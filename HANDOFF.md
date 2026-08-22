# Handoff

## [WP-STATE] Current State

> The repository contains the complete foundational document suite. No application code exists. The project is ready for stack decision and Phase 2 scaffold.

**Phase 1 is complete.** Delivered:
- `INTENT.md` — product purpose, actors, principles, and boundary. Platform philosophy locked.
- `SCENARIOS.md` — 7 actor-driven scenarios covering the full v1 teacher workflow. Stack-agnostic.
- `CAPABILITIES.md` — 8 capabilities derived from scenarios. No hardcoded curriculum.
- `GAPS.md` — 8 open questions and parked scope items. G-001 (stack decision) is the immediate blocker.
- `GOTCHAS.md` — 5 failure capsules. The platform-vs-curriculum confusion is the most critical one to preserve.
- `PROPOSAL.md` — prior architecture comparison (Option A / B / C). Still valid as input to stack decision, superseded by `ARCHITECTURE.md` once that decision is made.
- `preview/` — interactive HTML prototype of prior UI exploration. Reference only.

## [WP-MENTAL-MODEL] The One Thing a New Agent Must Absorb

> Read `INTENT.md` [INT-PRINCIPLES] before reading anything else.

The platform is a workspace tool — like Microsoft Word, not a pre-typed PDF. It does not know what the school teaches, how it assesses students, or what its timetable looks like. The school defines all of that inside the platform. Iqra', Hafazan, and Solat are examples of what a school might configure — not built-in features.

Any code, schema, or UI that hardcodes a subject name, assessment rubric, or curriculum concept is wrong by definition. See `GOTCHAS.md` entry 1.

## [WP-BLOCKERS] Immediate Blockers Before Phase 2

1. **G-001: Stack decision** — Option A (Filament v4 alone) is the natural fit for v1 (teacher-only, no parent portal). Needs operator confirmation. Document in `ARCHITECTURE.md`.
2. **G-002: Custom field type scope** — confirm the minimum field type set for v1 before building the profile and assessment builders.
3. **G-008: Offline behaviour decision** — state connectivity requirement before scaffold.

## [WP-NEXT] Next Action

> Operator confirms stack. Agent writes `ARCHITECTURE.md`. Phase 2 scaffold begins.

Sequence after stack confirmation:
1. Write `ARCHITECTURE.md` — stack rationale citing capability IDs, DB model approach, security boundaries.
2. Write `IMPLEMENTATION.md` — phase sequence, boundary-first execution rules.
3. Write `RECIPES.md` — install, dev server, test commands for the chosen stack.
4. Write `QUALITY-GATES.md` — what must pass before Phase 2 is called done.
5. Overhaul `README.md` — route map and current status table.
6. Begin Phase 2: Laravel 12 scaffold, migrations, Filament panel setup.
