# Code-Level Design

This document turns the eight stated capabilities into code contracts. It describes the intended implementation boundary; a statement here is not a claim that the code already exists.

## Design rules

- Laravel is the application boundary. Filament pages/resources are adapters over domain actions and policies.
- `school_id` is carried through every school-owned command, query, model, policy, migration, and test.
- Curriculum names are data, never migration columns or hardcoded enum values.
- A write has one authoritative action/service, one authorization check, and one audit outcome.
- Historical records are immutable after the save transaction commits.

## Capability-to-code map

| Capability | Primary code objects | Persistence | Acceptance contract |
|---|---|---|---|
| Profile builder | `ProfileSchemaResource`, `ProfileSchema`, `ProfileSchemaCompiler` | `profile_schemas` with versioned JSON schema | New fields are additive; removed fields are archived |
| Student registry | `StudentResource`, `Student`, `RegisterStudent`, `StudentPolicy` | `students`, profile values, object storage | MyKid uniqueness is school-scoped; deactivation preserves history |
| Cohorts | `CohortResource`, `Cohort`, `AssignStudentToCohort` | `cohorts`, membership/assignment records | Transfers do not rewrite historical attendance or assessment ownership |
| Timetable | `TimetableResource/Page`, `Timetable`, `TimetableSlotData` | `timetables` and JSON slots or normalized slots | Valid time ranges; today is derived from school timezone |
| Attendance | `CohortAttendance`, `RecordAttendance`, `AttendancePolicy` | `attendance_records` | One student/cohort/date record; post-lock change is an audited correction |
| Assessment builder | `AssessmentSchemaResource`, `AssessmentSchema`, `SchemaValidator` | versioned assessment schema JSON | Schema edits never mutate saved session payloads |
| Assessment record | `RecordAssessmentSession`, `AssessmentRecordPolicy` | append-only `assessment_records` | Corrections point to the original and cannot replace it |
| Query/report | `StudentRecordQuery`, `AttendanceSummaryQuery`, `AssessmentHistoryQuery` | indexed relational queries | Empty result is explicit; teacher scope is enforced in the query |
| Roles/access | panel providers, policies, permission checks, audit action | users/roles/audit log | Admin all-school; teacher assigned cohorts; parent is future read-only scope |

## Shared domain contracts

### Tenant context

`SchoolContext` resolves the authenticated actor's active school. Every action accepts the context explicitly or obtains it from the authenticated request and rejects a missing/mismatched school. Tests must include two schools and prove cross-school reads and writes fail.

### Schema versioning

Profile and assessment schemas use a stable schema identifier plus a monotonically increasing version. A record stores the schema version used at save time. The compiler accepts only allow-listed field types:

```text
text | number | status | date | checkbox
```

Field keys are machine-safe, labels are display data, and status options are school-owned data. A schema change creates a new version; it does not rewrite historical JSON.

### Audit contract

Every privileged or corrective write records actor, school, action, subject type/id, reason when required, and timestamp. Audit writes occur in the same database transaction as the business write whenever possible.

## Capability flows

### Student registration

1. `StudentResource` loads the active profile schema through `ProfileSchemaCompiler`.
2. `RegisterStudent` validates standard and configured fields.
3. It verifies school-scoped MyKid uniqueness and cohort membership.
4. It stores the student and photo reference in one transaction; the object upload is finalized only after authorization and validation succeed.
5. It emits an audit record and returns the student identifier.

Draft state is explicit. A draft cannot appear in an active attendance roster.

### Attendance

`CohortAttendance` loads the roster through a school/cohort-scoped query and submits a batch to `RecordAttendance`.

- The unique key is `(school_id, student_id, attendance_date)`.
- The action rejects students outside the teacher's assigned cohort.
- Before the configured lock time, a teacher may amend today's record.
- After lock, only an admin correction action may amend it, with a required reason and audit record.
- Attendance summaries use database aggregation, not PHP-side manual totals.

### Assessment

`AssessmentSchemaCompiler` translates versioned JSON into Filament components and validation rules. `RecordAssessmentSession` validates the submitted payload against the selected version, checks student/cohort scope, and inserts an immutable record. A correction stores `corrects_id`, `is_correction`, and the correction reason; no update path is exposed for the original record.

### Timetable

Timetable slots are school/cohort-owned records with `day_of_week`, `start_time`, `end_time`, `label`, and optional `category`. The write action rejects overlapping slots within a cohort unless an explicit override policy is later accepted. The dashboard derives today's day from the school's configured timezone; it does not store a second “today” value.

### Query and reporting

Queries are named read services, not controller-local query fragments:

- `StudentRecordQuery::forStudent()`
- `AttendanceSummaryQuery::forCohort()`
- `AssessmentHistoryQuery::forStudentAndSchema()`
- `ClassOverviewQuery::forDateRange()`

Each query begins with school scope, applies actor scope, then applies filters. Export and PDF services consume query DTOs so reports cannot bypass authorization.

## UI contracts

- Attendance is optimized for one-hand tablet use: status controls are native buttons, keyboard operable, and at least 44px square.
- Long Malay labels wrap; no fixed-height label container may clip text.
- Empty tables explain whether no records exist or filters exclude records.
- Destructive/deactivating actions require confirmation and preserve historical references.
- Parent-facing UI is deferred; do not add parent navigation or permissions until its ADR is accepted.

## Error and recovery behavior

- Validation errors remain on the form with field-level messages.
- Authorization failures are indistinguishable from missing records to unauthorized actors.
- Duplicate submissions are idempotent where a natural key exists.
- Queue failures are retried with bounded attempts and recorded in failed jobs.
- Object-storage failure prevents the business record from claiming a completed upload.
- Database transaction failure returns no success receipt.

## Implementation order

1. Normalize Laravel version and install/runtime verification.
2. Establish school, actor, policy, audit, and test factories.
3. Implement profile/student/cohort vertical slice.
4. Implement attendance/timetable vertical slice.
5. Implement schema compiler and append-only assessment slice.
6. Implement reports/exports and durable object storage.
7. Add Cloud Run packaging and deployment verification.
