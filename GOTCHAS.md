# Gotchas

> Known failure modes, wrong turns, and their permanent fixes.
> Format: Symptom → Root Cause → Permanent Fix → Verification.

---

## The platform prescribes curriculum instead of enabling it

**Symptom:** Scenarios or capabilities mention specific subject names, assessment rubrics, Jilid numbers, Surah names, or Solat steps as built-in features — not as examples.

**Root Cause:** The builder confused the school's domain knowledge (what to teach, how to assess it) with the platform's responsibility (how to store and surface whatever the school defines). The platform is a workspace tool, not a curriculum system.

**Permanent Fix:** Every time a scenario, capability, or data model names a specific Islamic or academic concept as a hardcoded field — stop. Replace it with the generic mechanism: a configurable field, a named assessment area, a custom status option. Iqra' and Hafazan are examples of how a school might use `CAP-ASSESSMENT-BUILDER`. They are never built-in features.

**Verification:** Read `INTENT.md` [INT-PRINCIPLES]. Re-read the scenario or capability. Ask: "Does this tell the school how to run their curriculum?" If yes, it belongs to the school, not the platform.

---

## Stack is chosen before capabilities are written

**Symptom:** A technology decision (Filament v4, Vue 3, Inertia) is made and justified before scenarios and capabilities exist to inform it.

**Root Cause:** Stack selection is a familiar, concrete task. Scenarios and capabilities feel abstract. The temptation is to pick a stack first and build scenarios around it.

**Permanent Fix:** Tombstone `INTENT.md` → `SCENARIOS.md` → `CAPABILITIES.md` before any stack discussion. The stack decision lives in `ARCHITECTURE.md` and must cite capabilities as justification — not the other way around.

**Verification:** `ARCHITECTURE.md` exists and references specific capability IDs as the reason for each stack component. `SCENARIOS.md` and `CAPABILITIES.md` contain no technology names.

---

## Capabilities are written as feature lists, not actor needs

**Symptom:** A capability reads like a product backlog item — "the system shall have a dashboard", "the system shall send notifications" — with no actor, no scenario derivation, and no boundary statement.

**Root Cause:** Writing capabilities from what "feels right" for a school app, rather than deriving them from scenarios that describe what an actor needs to accomplish.

**Permanent Fix:** Every capability must cite at least one scenario in its `Derived from` field. If a capability cannot be traced to a scenario, either the scenario is missing or the capability is not justified.

**Verification:** Every `CAP-*` entry in `CAPABILITIES.md` has a `Derived from` line with at least one `SCN-*` reference.

---

## Assessment records are overwritten instead of appended

**Symptom:** Editing a student's assessment entry replaces the previous value. Historical progress is lost. The system shows only the current state.

**Root Cause:** Assessment records modelled as mutable fields (UPDATE semantics) rather than as append-only session logs (INSERT semantics).

**Permanent Fix:** Each assessment session creates a new timestamped record row. Previous records are never updated in place. Corrections are new entries with a correction flag. The UI shows a timeline, not a single current value.

**Verification:** Database schema has no UPDATE path on assessment session records. Corrections produce a new row. The query for a student's assessment history returns all entries in chronological order.

---

## Timetable assumes a fixed national curriculum structure

**Symptom:** The timetable builder ships with pre-filled subject names, fixed time slots, or a day structure that matches one school's schedule — not a blank configurable grid.

**Root Cause:** The builder used a specific school's timetable (or a KP2026 example) as the default, rather than a blank template with optional standard labels.

**Permanent Fix:** The timetable grid ships blank. Standard slot type labels (Subject, Recess, Assembly, Free Play) are offered as optional starting-point suggestions — not pre-filled defaults. The teacher names everything.

**Verification:** A new teacher who has never used the platform can build a completely different timetable structure from scratch without removing any platform-imposed content.
