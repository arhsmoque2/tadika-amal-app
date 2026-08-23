# Capabilities

> Capabilities are derived from scenarios. Each one describes what the platform must be able to do — not what curriculum it knows.
> Iqra', Hafazan, and Solat are examples of how a school might use these capabilities. They are not built-in features.

---

## [CAP-PROFILE-BUILDER] Build and Maintain a Student Profile Structure

> The platform can provide a configurable student profile template that an admin defines once and that all student records in the school follow.

**Derived from:** `SCN-PROFILE-SETUP`, `SCN-STUDENT-REGISTER`

**Must make possible:** default starting fields (Name, MyKid, DOB, Gender, Address, Photo, Guardian, Phone); add custom fields of various types; group fields into named card sections; update the template without destroying existing records; archive removed fields without deleting their data.

**Boundary:** The platform provides the structure mechanism. The school provides the field names and decides what is required. The platform must never assume what a school needs to know about its students beyond what is explicitly configured.

---

## [CAP-STUDENT-REGISTRY] Register and Manage Students

> The platform can create, update, and deactivate student records using the school's configured profile template.

**Derived from:** `SCN-STUDENT-REGISTER`, `SCN-QUERY`

**Must make possible:** new student registration against the configured template, photo upload, cohort assignment, required-field enforcement, duplicate MyKid detection, draft save before completion, deactivation without data loss, and search/filter of the roster by name, class, or status.

**Boundary:** The student registry is the authoritative reference for every other capability. No capability may operate on a student that does not exist in the registry.

---

## [CAP-COHORT-MANAGEMENT] Manage Class Cohorts and Teacher Assignments

> The platform can create and maintain class cohorts and assign students and teachers to them.

**Derived from:** `SCN-STUDENT-REGISTER`, `SCN-TIMETABLE-SETUP`, `SCN-ATTENDANCE`, `SCN-ASSESSMENT-RECORD`

**Must make possible:** cohort creation with a name and academic year, student assignment and bulk transfer between cohorts, teacher assignment to one or more cohorts, and cohort archival at term end without losing historical records.

**Boundary:** The platform manages cohort membership. It does not define what a cohort means educationally or what age group it maps to — that is the school's naming decision.

---

## [CAP-TIMETABLE-BUILDER] Build and Display the Class Timetable

> The platform can provide a blank timetable grid that a teacher fills with their own slot names and times, and display today's schedule automatically.

**Derived from:** `SCN-TIMETABLE-SETUP`

**Must make possible:** blank weekly grid with configurable time slots; standard slot type labels as optional starting points (Subject, Recess, Assembly, Free Play); teacher-defined slot names, start times, and end times; calendar sync to today's date; and automatic display of today's timetable when the teacher opens the platform.

**Boundary:** The platform provides the grid and the calendar sync. It does not validate, suggest, or impose slot names, subject sequences, or daily structure. The school's timetable is the school's decision.

---

## [CAP-ATTENDANCE] Record and Accumulate Daily Attendance

> The platform can present the class roster for today, accept a present/absent mark per student, store the record with a timestamp, and accumulate records across all school days without manual totalling.

**Derived from:** `SCN-ATTENDANCE`, `SCN-QUERY`

**Must make possible:** auto-dated daily roster, Hadir/Tidak Hadir mark per student, optional free-text absence reason, teacher edit until end of school hours, admin override with correction log after lock, and attendance accumulation queryable across any date range.

**Boundary:** Attendance is a binary daily record (present or absent) with an optional reason. The platform does not model sessions within a day, late arrivals, or partial attendance unless explicitly configured as a custom field.

---

## [CAP-ASSESSMENT-BUILDER] Define a Custom Assessment Structure

> The platform can let a teacher or admin define named assessment areas with custom fields and input types, which become the recording template for student progress sessions.

**Derived from:** `SCN-ASSESSMENT-SETUP`, `SCN-ASSESSMENT-RECORD`

**Must make possible:** named assessment areas, custom field names, field types (Text / Number / Status selector / Date / Checkbox), custom status option labels, multiple assessment areas per class, and structure editing without destroying existing records.

**Boundary:** The platform provides the field types and the recording mechanism. It does not know what an assessment area means, what the correct status options are, or what a good result looks like. That is entirely the school's domain.

---

## [CAP-ASSESSMENT-RECORD] Record and Accumulate Student Assessment Sessions

> The platform can accept a teacher's assessment entry for a student against a configured assessment structure, timestamp it, and accumulate entries into a session history.

**Derived from:** `SCN-ASSESSMENT-RECORD`, `SCN-QUERY`

**Must make possible:** single-student and group entry, session entry against any configured assessment area, field-by-field input matching the configured structure, timestamp per entry, append-only history (no silent overwrite), and correction entries that retain the original.

**Boundary:** Each saved session entry is a permanent record. The platform never overwrites a historical assessment entry. Corrections are new entries with a correction flag, not replacements.

---

## [CAP-QUERY-REPORT] Query and Review Any Record at Any Time

> The platform can return any combination of student attendance records, assessment session history, and profile data — filtered by student, class, date range, or assessment area — in seconds.

**Derived from:** `SCN-QUERY`

**Must make possible:** per-student view of attendance history with totals and rate, per-student view of all session entries for any assessment area in chronological order, class-wide attendance summary for any date range, class-wide assessment overview across students, and explicit empty state when no records match.

**Boundary:** The query capability surfaces what teachers and admins have recorded. It does not infer, summarise, or score. A zero attendance rate means the record shows zero — it does not mean the platform guesses a reason.

---

## [CAP-ROLES-ACCESS] Enforce Role-Based Access Control

> The platform can restrict each actor to the data and operations their role permits, without requiring code changes when roles are assigned or revoked.

**Derived from:** all scenarios

**Must make possible:** three baseline roles — Admin (full configuration and all-class access), Teacher (own class data entry and query), and future Parent role (read-only, scoped to own children); role assignment by admin; permission enforcement on all read and write operations; and an audit log of who performed which write operation.

**Boundary:** Role configuration is admin-managed within the platform. Teachers may only read and write records for students in their assigned cohort. No actor may access another family's or class's data outside their permitted scope.
