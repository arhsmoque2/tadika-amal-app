# ADR-003: JSON Schema for Runtime-Configurable Fields

**Status:** Accepted

## [ADR-003-CONTEXT] Context

> `CAP-PROFILE-BUILDER` and `CAP-ASSESSMENT-BUILDER` require that an admin or teacher can define their own field names, field types, and status options at runtime — without code changes or deployments. This is a dynamic schema problem. No framework ships this out of the box.

Two approaches were considered:
- **EAV (Entity-Attribute-Value)**: separate rows per field per record. Flexible but produces complex queries and poor performance at scale.
- **JSON schema + JSON column**: field definitions stored as a JSON schema document; record values stored in a JSON column on the main table.

## [ADR-003-DECISION] Decision

> Store field schema definitions as a JSON document in a `schema` column on the configuration table. Store student profile values and assessment session values in a `data` JSON column on the respective record tables. Filament forms are constructed programmatically at runtime from the stored schema.

Schema definition UI uses Filament's `Repeater` component — admins add/remove field rows (name, type, options) which are serialised as JSON on save. At record-entry time, PHP reads the schema and constructs a Filament `Form` dynamically, field by field.

Supported field types for v1: `text`, `number`, `select` (with admin-defined options), `date`, `checkbox`, `textarea`.

## [ADR-003-CONSEQUENCES] Consequences

> Record data is stored as JSON, not in typed relational columns. This means full-text search and complex SQL aggregation against field values requires JSON path queries or application-layer processing.

Query performance on large datasets may degrade. For v1 school sizes (< 500 students, < 5 years of records), JSON column queries on PostgreSQL or MySQL are acceptable.

If a field is removed from the schema, its data key remains in existing JSON records (orphaned but not lost). Added fields appear as `null` in existing records. Schema versions are not tracked in v1 — this is `GAP G-003` and must be revisited before the platform is used by multiple schools with diverging schemas.

A future migration to typed columns for high-frequency fields (e.g., attendance status) is not precluded by this decision.
