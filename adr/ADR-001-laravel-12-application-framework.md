# ADR-001: Laravel 12 as the Application Framework

**Status:** Accepted

## [ADR-001-CONTEXT] Context

> Tadika Amal Apps requires a server-rendered, database-backed application with role-based access control, file uploads, and a structured admin interface. The primary actor for v1 is a teacher operating within a school intranet or cloud-hosted environment.

The platform needs a mature PHP framework with strong ORM support, a plugin ecosystem for admin panel construction, and straightforward deployment on shared or VPS hosting — which is the realistic hosting context for Malaysian school operators.

## [ADR-001-DECISION] Decision

> Use Laravel 12 as the sole application framework.

Laravel provides Eloquent ORM, migrations, queued jobs, file storage abstraction (local and S3-compatible), Artisan CLI, Fortify-compatible authentication, and a first-class Filament v4 integration. It is the most deployed PHP framework in the Filament ecosystem and has the widest local developer pool for Malaysian projects.

## [ADR-001-CONSEQUENCES] Consequences

> The application is a single Laravel monolith for v1. No microservices, no separate API layer, no separate frontend build unless explicitly added in a future ADR.

PHP 8.2+ is the minimum runtime. Composer manages all server-side dependencies. A future decision to add an Inertia.js parent portal (v2) must update this ADR and `ARCHITECTURE.md`.
