# Architecture

## [ARCH-BOUNDARY] System Boundary

> Tadika Amal Apps is a modular preschool and kindergarten operations platform. It runs as a monolithic Laravel 12 application with a Filament v4 operational panel serving Teachers, Headmasters, and Administrators.

```text
Teacher / Admin Web Browser
  -> Google Cloud Run (Container: PHP 8.4 + FrankenPHP / Octane, asia-southeast1)
      -> Authentication / Role Middleware (Spatie Permission, Multi-Tenant school_id Scope)
      -> Filament v4 Admin/Teacher Workspace
          -> Dynamic Schema Engine (JSON Schema -> Livewire / Filament Forms)
          -> Universal Import/Export Engine (waadmawlood/import-wizard & pxlrbt/excel)
          -> Multi-Format Document Engine (PHPWord, PHPPresentation, mPDF Posters)
          -> Eloquent Models (Tenant Scoped by school_id)
      -> Neon Serverless PostgreSQL (ap-southeast-1)
          -> Pooled connection for web runtime (DB_URL)
          -> Direct connection for migrations & CLI (DB_URL_DIRECT)
          -> Ephemeral PR preview database branches
      -> Cloudflare R2 Object Storage (Private Bucket, S3 API)
          -> Student photos, document attachments, generated exports
          -> Served via short-lived presigned URLs
      -> Cloud Run Jobs + Cloud Scheduler
          -> php artisan schedule:run (every minute)
          -> Database Queue Worker for background document generation
```

---

## [ARCH-PROCESS] Process Model & Topology

> Stateless containerized architecture deployed on Google Cloud Run in project `arh-gcloud-vm` (`asia-southeast1`).

- **Web Server**: Stateless PHP 8.4 container with FrankenPHP / Laravel Octane handling HTTP traffic.
- **Worker Queue**: Database queue driver (`QUEUE_CONNECTION=database`) backed by Neon PostgreSQL; handles document generation, async bulk imports, and report compiling without Redis overhead.
- **Scheduler**: Cloud Scheduler invoking Cloud Run Jobs on a 1-minute cron (`* * * * *`).
- **Static Assets**: Pre-compiled Vite assets (Filament CSS/JS) served directly from the container image.
- **Zero Local State Invariant**: Local `/tmp` and local disk are ephemeral and wiped across container scaling events. All uploads and generated documents stream directly to Cloudflare R2.

---

## [ARCH-API] Domain Data Model

> Strict isolation between core organizational identity and dynamic curriculum evaluation schemas.

### Core Identity & School Structure
1. `schools` / `tenants`: Primary isolation boundary (`id`, `name`, `code`, `created_at`).
2. `users`: System actors (`id`, `school_id`, `name`, `email`, `role`, `password`, `created_at`).
3. `cohorts`: Class groupings (`id`, `school_id`, `name`, `academic_year`, `teacher_id`, `capacity`).
4. `students`: Enrolled children (`id`, `school_id`, `cohort_id`, `name`, `mykid`, `photo_path`, `data` [JSON], `created_at`).
5. `attendances`: Daily attendance logs (`id`, `school_id`, `cohort_id`, `student_id`, `date`, `status` [Hadir/Tidak Hadir], `reason`, `recorded_by`).

### Operational Modules (Preschool SIS & Finance)
1. `health_records`: Daily morning triages, BMI checks, medication tracking, and allergy logs (`id`, `school_id`, `student_id`, `recorded_by`, `data` [JSON], `created_at`).
2. `incident_logs`: Safety, minor injuries, and behavioral incident logs with parent signature verification (`id`, `school_id`, `student_id`, `description`, `action_taken`, `created_at`).
3. `milestones`: Developmental milestones aligned with National Preschool Standard Curriculum (KSPK) (`id`, `school_id`, `student_id`, `domain`, `status`, `achieved_at`).
4. `fee_structures` & `fee_invoices`: Configurable tuition packages and generated billing records (`id`, `school_id`, `student_id`, `invoice_no`, `amount`, `status`, `due_date`).

### Dynamic Schema & Assessment History
1. `profile_schemas`: Dynamic field specifications for customized student registry cards (`id`, `school_id`, `schema` [JSON], `created_at`).
2. `assessment_schemas`: Curriculum domain blueprints (`id`, `school_id`, `name`, `fields` [JSON], `created_at`).
3. `assessment_records`: Append-only assessment history (`id`, `school_id`, `assessment_schema_id`, `student_id`, `cohort_id`, `recorded_by`, `data` [JSON], `is_correction`, `corrects_id`, `created_at`).
4. `timetables`: 5-day blank-grid schedules (`id`, `school_id`, `cohort_id`, `day_of_week`, `slots` [JSON], `updated_at`).

---

## [ARCH-STORAGE] Dynamic Schema & Storage Engine

### 1. Dynamic Schema Engine (ADR-003)
Field definitions are stored as JSON specifications and compiled into Filament Form components dynamically at runtime:
```json
{
  "section": "Maklumat Kesihatan & Alergi",
  "fields": [
    {
      "name": "jenis_alergi",
      "label": "Jenis Alergi / Makanan Dilarang",
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

### 2. Object Storage (ADR-009 — Cloudflare R2)
- Driver: Standard Laravel `s3` disk over Flysystem.
- Bucket: Private R2 bucket with endpoint `https://<ACCOUNT_ID>.r2.cloudflarestorage.com`.
- Presigned Delivery: Short-lived temporary URLs generated for teacher/admin asset access. Zero egress bandwidth charges.

---

## [ARCH-SECURITY] Security & Role Boundaries

> Access is governed by Spatie Laravel Permission and Filament Tenant scopes.

1. **Multi-Tenant Isolation**: Every query, mutation, export, and import is scoped to `school_id`. Teachers access only their assigned cohorts.
2. **Keyless CI/CD (WIF)**: Deployment from GitHub Actions to Cloud Run uses Workload Identity Federation (`urus-github-pool` / `tadika-deployer` in project `arh-gcloud-vm`) with zero static GCP keys.
3. **Audit & Append-Only Integrity**: Assessment records cannot be edited in-place; corrections spawn new linked records with parent revision tracking (ADR-004).
4. **Secrets Invariant**: Production secrets (`APP_KEY`, `NEON_API_KEY`, R2 credentials, DB connection URLs) are stored in SOPS / GitHub Secrets and injected at container runtime.

---

## [ARCH-INVARIANTS] Runtime Invariants

- **Zero Data Loss on Migration**: Import engine performs upsert de-duplication based on deterministic keys (`mykid`, `staff_no`, `invoice_no`).
- **Zero Local File Dependency**: No process may write persistent data to the local disk.
- **Ephemeral Preview Parity**: Pull requests automatically spawn a disposable Neon database branch for migration verification and drop it upon merge/close.
- **Touch Target Accessibility**: All interactive operational screens maintain $\ge 44\text{px} \times 44\text{px}$ touch targets.
