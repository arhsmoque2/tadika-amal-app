# Gaps

> Open questions, unresolved design decisions, and deliberately parked scope.
> Items here are explicit so a passing build is not mistaken for a complete product.

---

## G-001: Stack decision not yet made

**Status:** Open

The technology stack for Phase 2 (repository scaffolding) has not been locked. Three options were evaluated in `PROPOSAL.md`:
- Option A: Laravel 12 + Filament v4 only
- Option B: Laravel 12 + Inertia.js + Vue 3
- Option C: Hybrid (Filament backoffice + Vue parent portal)

For v1 (Teacher module only, no parent portal), Option A is the natural fit. This must be confirmed before any scaffold begins.

**Acceptance:** A stack decision is recorded in `ARCHITECTURE.md` with rationale. `PROPOSAL.md` is superseded by that decision.

---

## G-002: Custom field types scope for v1

**Status:** Open

`CAP-ASSESSMENT-BUILDER` and `CAP-PROFILE-BUILDER` require configurable field types. The minimum viable set for v1 has not been confirmed:
- Text, Number, Status selector, Date, Checkbox — sufficient for v1?
- File attachment, Photo per field, Dropdown with dependent fields — v2?

**Acceptance:** A confirmed field type set is documented in `ARCHITECTURE.md` before assessment builder is implemented.

---

## G-003: Assessment structure versioning

**Status:** Open

When a teacher edits an existing assessment structure (renames a field, adds a new option), historical records were created against the old structure. The platform must decide how to handle this:
- Option A: Old records remain as-is; new records use the new structure (fields may not match).
- Option B: Structure versions are stored; records are always rendered against the version active when they were created.

**Acceptance:** A versioning policy is recorded in `ARCHITECTURE.md` before assessment builder is implemented.

---

## G-004: Timetable recurrence model

**Status:** Open

The timetable builder must decide whether timetables are:
- **Weekly repeating**: one template repeats every week (simplest).
- **Day-specific overrides**: a base template with exceptions for specific dates (public holidays, events).

**Acceptance:** A recurrence model is confirmed before timetable builder is implemented.

---

## G-005: Parent portal

**Status:** Parked — v2

Parents are a named secondary actor in `INTENT.md` but are explicitly out of scope for v1. The parent portal — child status view, diary feed, invoice and payment — is parked until the teacher module is validated with real users.

**Trigger to revisit:** v1 teacher module is in active use by at least one school.

---

## G-006: Fee billing and payment

**Status:** Parked — v2 or v3

Fee invoice generation, FPX gateway integration (ToyyibPay/Billplz), and PDF receipt generation are out of scope for v1. The billing capability defined in the earlier `CAPABILITIES.md` has been removed from the current scope.

**Trigger to revisit:** Admin panel is in place and school management actor is in scope.

---

## G-007: WhatsApp alerts

**Status:** Parked — v2

Automated WhatsApp notifications for attendance, fees, and announcements are out of scope for v1. The capability requires a parent actor (G-005) to be meaningful.

---

## G-008: Offline / low-connectivity behaviour

**Status:** Open — decision needed before scaffold

Malaysian school environments may have intermittent connectivity. The platform must decide before scaffold whether v1 requires offline-capable attendance taking (e.g., PWA with local queue) or whether connectivity is a prerequisite.

**Acceptance:** Connectivity requirement stated in `ARCHITECTURE.md`.
