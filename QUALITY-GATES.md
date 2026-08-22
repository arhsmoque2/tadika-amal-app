# Quality Gates

## [QG-LOCAL] Local Transition Gate

> A feature slice or pull request may only be handed off when database migrations, Pint linting, and Pest automated tests pass.

```text
PASS: vendor/bin/pint --test
PASS: php artisan test
```

---

## [QG-DOCS] Documentation Conformance Gate

> Every architectural modification must maintain consistency across `INTENT.md`, `ARCHITECTURE.md`, `DESIGN.md`, `IMPLEMENTATION.md`, and `HANDOFF.md`.

Rules:
1. Every new or updated capability must originate from an actor scenario in `SCENARIOS.md`.
2. No hardcoded syllabus or curriculum specifics are allowed in database seeders or core column definitions.
3. Every completed milestone must record verification receipts in `HANDOFF.md`.

---

## [QG-SCHEMA] Dynamic Schema Safety Gate

> The dynamic schema engine must guarantee:

- Existing records with missing JSON keys deserialize gracefully with `null` fallback.
- Removing a field from a schema definition does not trigger deletion or migration of historical JSON properties.
- Assessment session record models reject any programmatic `UPDATE` invocations unless marked as an explicit correction record.
