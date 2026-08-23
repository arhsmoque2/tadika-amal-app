# ADR-002: Filament v4 as the Admin Panel — v1 Only

**Status:** Accepted

## [ADR-002-CONTEXT] Context

> v1 of Tadika Amal Apps targets the teacher actor only. There is no parent-facing portal, no consumer mobile UX, and no billing interface in v1. The required capabilities are data-entry heavy: student registry, cohort management, attendance, assessment recording, and queryable reports.

Three options were evaluated against the v1 capability set (see `stack-evaluation` in session artefacts). The evaluation was done capability-by-capability, not by technology preference.

## [ADR-002-DECISION] Decision

> Use Filament v4 as the sole UI layer for v1. No Inertia.js, no Vue, no React frontend for v1.

Filament provides tables, filterable resource lists, form construction, file upload, role-scoped panel access, and navigation — all for free — around the two capabilities that require custom build work (`CAP-PROFILE-BUILDER`, `CAP-ASSESSMENT-BUILDER`). A Vue/Inertia frontend would require building all of that from scratch in addition to the custom schema work.

The one capability where Vue is marginally better (`CAP-TIMETABLE-BUILDER` — interactive grid) does not justify adding a second frontend stack and build pipeline for v1.

## [ADR-002-CONSEQUENCES] Consequences

> Filament v4 is the UI contract for v1. All teacher-facing and admin-facing surfaces are Filament panels and pages.

Spatie Laravel Permission is the standard role and permission layer for Filament. Two baseline roles for v1: Admin and Teacher.

This decision is explicitly scoped to v1. When the parent portal joins (v2), a second ADR will introduce Inertia.js + Vue 3 alongside the existing Filament panel — same Laravel app, same database, separate panel routes. This ADR must be updated at that point.
