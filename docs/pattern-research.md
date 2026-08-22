# Pattern Research: school.ly

> Source repository: `https://github.com/mhmdhussein/school.ly`
> Local reference clone: `D:\_AGENT-WORKSPACE\lab-test-scratchpad\school.ly`

## [RES-CONTEXT] Research Context

`school.ly` is an educational reference implementation built on Laravel 12. This document reviews its structural patterns, dependencies, and architectural decisions to identify reusable mechanisms vs anti-patterns for Tadika Amal Apps.

---

## [RES-PATTERNS] Adoptable Patterns

### 1. Multi-Tenant Scoping (`tenant_id`)
* **Observed**: Every model (`Student`, `Teacher`, `Course`, `Enrollment`) carries a `tenant_id` foreign key and queries are scoped via `Auth::user()->tenant_id` in controllers.
* **Tadika Amal Adoption**: In Tadika Amal, multi-tenancy corresponds to `school_id` / `institution_id`. For v1, multi-tenant scoping ensures that a school's custom schemas and student lists remain strictly isolated.

### 2. Built-in Fortify Auth & 2FA Scaffold
* **Observed**: Full Laravel Fortify integration with two-factor authentication (TOTP), recovery codes, and profile update flows out of the box.
* **Tadika Amal Adoption**: Tadika Amal leverages this robust baseline for teacher/admin authentication.

### 3. Clean Separation of Models and Resource Handlers
* **Observed**: Standard Laravel structure with dedicated Controllers, Form Requests, and Seeders.

---

## [RES-DIVERGENCE] Architectural Divergences & What NOT to Adopt

### 1. Hardcoded Relational Columns for Domain Entities
* **Observed**: `school.ly` hardcodes columns like `first_name`, `last_name`, `grade`, `subject` directly in database migrations.
* **Tadika Amal Constraint**: Tadika Amal's core philosophy (see `INTENT.md` and `ADR-003`) is **platform, not syllabus** (the MS Word model). Domain fields and assessment attributes are dynamic JSON schemas defined at runtime, not static columns.

### 2. Stack Divergence for v1 (React/Inertia vs Filament v4)
* **Observed**: `school.ly` uses Inertia.js v2 with React 19 and `@laravel/vite-plugin-wayfinder`.
* **Tadika Amal Constraint**: As established in `ADR-002`, Tadika Amal v1 is strictly teacher/admin data-entry and operational management, making Filament v4 the highest-efficiency engine. The React/Inertia frontend from `school.ly` serves as a valuable architectural reference when implementing the Parent Portal in v2.
