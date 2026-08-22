# Pattern Research & Codebase Evaluation

> Comprehensive audit of candidate repositories and reference implementations evaluated to accelerate Tadika Amal Apps without building from scratch.

---

## [RES-1] Candidate Repositories Matrix

| Repository | Stack & Tools | Evaluated Role | Decision |
| :--- | :--- | :--- | :--- |
| **`jeffersongoncalves/filakitv4`** | Laravel 13 / Filament v4.5 / Livewire 3.7 / TailwindCSS v4 | **Base Skeleton** | **Adopted as Base Foundation** (Decoupled Schemas/Tables, Multi-Panel, PWA, Dev Logins). |
| **`AqibUllah/Laravel-Filament-Starter-Kit`** | Laravel 12 / Filament v4 / Shield / ActivityLog / Excel | **Feature Reference** | **Adopted Selected Capabilities** (Filament Shield RBAC, ActivityLog audit trail for ADR-004, Excel export). |
| **`felipereisdev/filament-crud-maker`** | Laravel 11-13 / Filament v4-v5 Generator Tool | **CLI Tooling & Accelerator** | **Adopted as Scaffolding Engine** (`make:filament-crud` for zero-boilerplate schema generation). |
| **`mhmdhussein/school.ly`** | Laravel 12 / Fortify / Inertia v2 / React 19 / TypeScript | **Architecture Reference** | **Adopted Architectural Patterns** (Multi-tenant scoping, Fortify 2FA flows). Frontend parked for v2 Parent Portal. |
| **`academico-sis/academico`** | Laravel 12 / Filament v5 / mPDF / PHPWord / Maatwebsite Excel | **Domain Scaffolding & Engine** | **Adopted Core SIS Modules** (Timetable Grid, Student Attendance Matrix, Teacher Dashboard, Skill Assessment Engine, DOCX/PDF Report Card Pipeline). |

---

## [RES-2] Detailed Repository Assessments

### 1. `jeffersongoncalves/filakitv4`
* **Source**: `https://github.com/jeffersongoncalves/filakitv4.git`
* **Local Reference**: `D:\_AGENT-WORKSPACE\lab-test-scratchpad\filakitv4`
* **Key Assets Adopted**:
  - **Filament v4 Architecture**: Decoupled `Schemas/` (`*Form.php`, `*Infolist.php`) and `Tables/` (`*Table.php`) directory structure adhering to Filament v4 best practices.
  - **Multi-Panel Engine**: Clean separation into `AdminPanelProvider`, `AppPanelProvider` (for teachers/staff), and `GuestPanelProvider`.
  - **PWA Integration**: `jeffersongoncalves/filament-pwa` resolves **G-008** (offline/mobile-friendly interface for classroom operations).
  - **Developer Tooling**: Integrated `dutchcodingcompany/filament-developer-logins` for rapid switching between roles in local testing, Larastan Level 9 static analysis, Laravel Pint, and Pest 4 test harness.

### 2. `AqibUllah/Laravel-Filament-Starter-Kit`
* **Source**: `https://github.com/AqibUllah/Laravel-Filament-Starter-Kit.git`
* **Local Reference**: `D:\_AGENT-WORKSPACE\lab-test-scratchpad\Laravel-Filament-Starter-Kit`
* **Key Assets Adopted**:
  - **Visual RBAC**: `bezhansalleh/filament-shield:^4.0` for Spatie Role & Permission management directly inside Filament UI.
  - **Audit Logging**: `pxlrbt/filament-activity-log` and `spatie/laravel-activitylog` enforcing ADR-004 audit trail on attendance and assessment edits.
  - **Excel Export**: `pxlrbt/filament-excel` for automated tabular student and attendance report exports.
* **Divergence / Omissions**:
  - Excluded SaaS billing (`filament/spark-billing-provider`, Stripe, Cashier), AI agents (`laravel/ai`), and MCP server components not required for v1 school operations.

### 3. `felipereisdev/filament-crud-maker`
* **Source**: `https://github.com/felipereisdev/filament-crud-maker.git`
* **Local Reference**: `D:\_AGENT-WORKSPACE\lab-test-scratchpad\filament-crud-maker`
* **Key Assets Adopted**:
  - **Artisan CRUD Generator**: `php artisan make:filament-crud {Model}` generating Model, Migration, Form Schema, and Table simultaneously.
  - **30+ Supported Field Types**: Native generation of JSON payloads, key-value maps, images, date pickers, and polymorphic/nested relationships.
  - **Filament v4 Mode B Conformance**: Emits decoupled Schema and Table class structures matching `filakitv4`.

### 4. `mhmdhussein/school.ly`
* **Source**: `https://github.com/mhmdhussein/school.ly`
* **Local Reference**: `D:\_AGENT-WORKSPACE\lab-test-scratchpad\school.ly`
* **Key Assets Adopted**:
  - **Tenant Scoping**: Global query scopes applying `school_id` / `institution_id` across models.
  - **Authentication**: Fortify 2FA and password recovery patterns.
* **Divergence**:
  - Rigid relational curriculum migrations (`grade`, `subject`, `first_name`) rejected in favor of dynamic JSON schema engine (**ADR-003**).
  - React 19/Inertia stack parked for v2 Parent Portal (**ADR-002**).

### 5. `academico-sis/academico`
* **Source**: `https://github.com/academico-sis/academico.git`
* **Local Reference**: `D:\_AGENT-WORKSPACE\lab-test-scratchpad\academico-temp`
* **Key Assets Adopted**:
  - **Timetable & Scheduling Matrix**: Ported `CourseTime` and `CalendarCombined` weekly multi-filter calendar for cohorts and teachers.
  - **Single-Click Attendance Grid**: Adapted `CourseAttendance` into `CohortAttendance` Livewire view for rapid classroom roll-call (Hadir, Tidak Hadir, Sakit, Cuti).
  - **Teacher Workspace**: `TeacherDashboard` pattern providing dedicated teacher overview with pending attendance flags and scheduled classes.
  - **Multi-Format Document Pipeline**:
    - **DOCX**: `phpoffice/phpword` `TemplateProcessor` generating registration forms and offer letters from templates.
    - **PDF**: `mpdf/mpdf` + Blade views for official preschool assessment dossiers and certificates.
    - **XLS**: `maatwebsite/excel` for fast tabular imports and exports.
    - **PPT/Media**: Spatie MediaLibrary plugin for classroom slides and attachments.
  - **Skill Evaluation Matrix**: Adapted `app/Models/Skills/` and `SkillEvaluationPage` for scoring Malaysian KSPK developmental domains.

---

## [RES-3] Conformance & Quality Checklist

1. [x] All 4 candidate repositories audited locally in `_AGENT-WORKSPACE/lab-test-scratchpad/`.
2. [x] Architectural alignment confirmed: Zero hardcoded curriculum tables; dynamic schema model preserved.
3. [x] Documented in `docs/pattern-research.md` and formalized in `adr/ADR-005` and `adr/ADR-006`.
