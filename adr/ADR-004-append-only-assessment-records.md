# ADR-004: Append-Only Assessment Session Records

**Status:** Accepted

## [ADR-004-CONTEXT] Context

> `CAP-ASSESSMENT-RECORD` requires that each saved assessment session entry is a permanent, timestamped record. Teachers must be able to see the full history of a student's progress across sessions — not just the current value. A mutable record model (UPDATE semantics) would silently destroy this history.

## [ADR-004-DECISION] Decision

> Assessment session records are append-only. Each save produces a new row with a timestamp. No UPDATE is permitted on a saved session record. Corrections are new rows with a `is_correction` flag and a `corrects_id` foreign key pointing to the original record.

The Eloquent model for assessment sessions will not expose an `update()` method in application code. The Filament resource will have no Edit action on past session entries — only a New Session action and a Correction action.

## [ADR-004-CONSEQUENCES] Consequences

> The assessment session table grows by one row per session per student, indefinitely. For v1 school sizes this is not a concern. Pagination on the history timeline view is required once a student has more than 50 session entries.

Querying "current state" for a student (e.g., latest Iqra' page) requires a `ORDER BY created_at DESC LIMIT 1` pattern, not a lookup on a single mutable field. This must be reflected in all query and report implementations.

Soft-delete is not applicable to session records. Records are never deleted — only superseded by correction entries.
