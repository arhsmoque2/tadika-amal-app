# Architecture

## [ARCH-BOUNDARY] System Boundary

> Tadika Amal Apps is a modular school operations platform. For v1, it runs as a monolithic Laravel 12 application with a Filament v4 panel serving Teachers and Administrators.

```text
Teacher / Admin Web Browser
  -> Laravel 12 HTTP Kernel
      -> Authentication / Role Middleware (Spatie Permission)
      -> Filament v4 Admin/Teacher Panel
          -> Dynamic Schema Engine (JSON Schema -> Filament Form)
          -> Eloquent Models (Multi-Tenant Scoped)
      -> PostgreSQL / MySQL Database
          -> Core tables (users, schools, cohorts, students, attendances)
          -> Config tables (profile_schemas, assessment_schemas)
          -> Session tables (assessment_records - append-only)
      -> Local / S3-compatible Storage (Passport photos, attachments)
```

---

## [ARCH-PROCESS] Process Model

> The initial release is a monolithic web service with optional background worker queues for maintenance and exports.

- **Web Server**: PHP 8.2+ running with Octane or standard PHP-FPM / Nginx.
- **Worker Queue**: Laravel default database/Redis queue worker for background reports and notifications.
- **Static Assets**: Vite-compiled Filament CSS/JS bundle.

---

## [ARCH-API] Domain Data Model

> The architecture enforces a strict separation between core operational identity and dynamic curriculum data.

### Core Tables
1. `schools` / `tenants`: Primary isolation boundary (`id`, `name`, `code`, `created_at`).
2. `users`: System actors (`id`, `school_id`, `name`, `email`, `role`, `password`, `created_at`).
3. `cohorts`: Groupings of students (`id`, `school_id`, `name`, `academic_year`, `teacher_id`).
4. `students`: Enrolled individuals (`id`, `school_id`, `cohort_id`, `name`, `mykid`, `photo_path`, `data` [JSON], `created_at`).
5. `attendances`: Daily log (`id`, `school_id`, `cohort_id`, `student_id`, `date`, `status` [Hadir/Tidak Hadir], `reason`, `recorded_by`).

### Dynamic Schema & Record Tables
1. `profile_schemas`: Definitions of custom student profile cards (`id`, `school_id`, `schema` [JSON], `created_at`).
2. `assessment_schemas`: Definitions of assessment domains (`id`, `school_id`, `name`, `fields` [JSON], `created_at`).
3. `assessment_records`: Append-only assessment history (`id`, `school_id`, `assessment_schema_id`, `student_id`, `cohort_id`, `recorded_by`, `data` [JSON], `is_correction`, `corrects_id`, `created_at`).
4. `timetables`: Grid schedules (`id`, `school_id`, `cohort_id`, `day_of_week`, `slots` [JSON], `updated_at`).

---

## [ARCH-STORAGE] Dynamic Schema Engine

> Follows **ADR-003**: Field definitions are stored as JSON specifications and compiled into Filament Form components dynamically at runtime.

```json
{
  "section": "Maklumat Tambahan",
  "fields": [
    {
      "name": "nama_panggilan",
      "label": "Nama Panggilan",
      "type": "text",
      "required": false
    },
    {
      "name": "kumpulan_darah",
      "label": "Kumpulan Darah",
      "type": "select",
      "options": ["A", "B", "AB", "O"]
    }
  ]
}
```

---

## [ARCH-SECURITY] Security & Role Boundaries

> Access is governed by Spatie Laravel Permission and Filament Tenant scopes.

1. **Isolation**: A teacher can only query or record data for students in their assigned `cohort_id` under their `school_id`.
2. **Admin Override**: Administrators can configure schemas, manage school-wide cohorts, and issue correction overrides.
3. **Data Integrity**: Assessment records are strictly append-only; standard API endpoints do not expose `UPDATE` routes for `assessment_records` (see **ADR-004**).

---

## [ARCH-INVARIANTS] Runtime Invariants

- Attendance records for a given date are locked at the close of school hours; late modifications create an audit record.
- Historical assessment records are never mutated; corrections spawn linked new rows.
- Schema removal does not delete existing JSON keys from student or assessment records.
