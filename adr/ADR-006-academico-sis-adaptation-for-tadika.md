# ADR-006: Academico SIS Adaptation for Timetable, Attendance, Document Pipeline & Assessment

**Status:** Accepted

## [ADR-006-CONTEXT] Context

To deliver full kindergarten operations without reinventing core SIS algorithms, we evaluated `academico-sis/academico` (a modern Laravel 12 + Filament SIS).

Tadika Amal Apps requires 5 primary functional engines:
1. **Timetable / Jadual Waktu**: Visual weekly grid per class (cohort) and per teacher, accounting for preschool subject blocks, recess, and teacher assignments.
2. **Attendance / Rekod Kehadiran**: Fast daily attendance recording (Students × Dates matrix) with status classifications (*Hadir, Tidak Hadir, Sakit, Cuti*), absent reasons, and compliance stats.
3. **Per-Teacher & Per-Class Admin Workspaces**: Dedicated teacher dashboards providing immediate access to assigned cohorts, pending attendance, and upcoming schedules.
4. **Document & Office Suite (PDF, DOCX, PPT, XLS)**: Automated letter generation (`.docx` templating), official preschool report cards (`.pdf` generation via mPDF/Blade), batch enrollment and attendance exports (`.xlsx`/`.csv`), and teaching slides/worksheets viewing (`.pptx`/`.pdf` via MediaLibrary).
5. **Annual Assessment Reports & Skill Matrix (KSPK)**: Standardized preschool developmental domain assessments (Kurikulum Standard Prasekolah Kebangsaan) using multi-tier skill scales (*Belum Menguasai / Sedang Menguasai / Menguasai*), qualitative notes, and printable annual progress dossiers.

## [ADR-006-DECISION] Decision

We adopt the proven architecture patterns from `academico-sis/academico` adapted to Tadika Amal's multi-tenant (`school_id`) and dynamic JSON schema container rules:

1. **Timetable / Schedule Module**:
   - Model `TimetableSlot` (and scheduled `Event` instances) capturing `cohort_id`, `teacher_id`, `room_id`, `day_of_week` (Isnin–Jumaat), `start_time`, `end_time`, `subject_name`, and `color`.
   - Reusable Livewire + Filament grid view rendering per-cohort and per-teacher weekly timetables.

2. **Matrix Attendance Engine**:
   - Model `AttendanceRecord` linking `student_id`, `cohort_id`, `date`, `status` (Enum: `hadir`, `tidak_hadir`, `sakit`, `cuti`), `reason`, and `recorded_by`.
   - Port `CourseAttendance` matrix pattern into `CohortAttendance` Livewire page for single-click cell updates across entire classes.

3. **Multi-Format Document Pipeline**:
   - **DOCX**: Integrate `PHPWord` `TemplateProcessor` (`DocumentTemplateService`) to inject student profile data into standardized `.docx` preschool registration and offer letters.
   - **PDF**: Integrate `mPDF` and Blade templates (`AssessmentReportPdfService`) for official Malaysian kindergarten report cards (*Laporan Perkembangan Murid / Sijil Tamat Prasekolah*).
   - **XLS/CSV**: Integrate `AttendanceSpreadsheetService` and Filament Exporters for bulk student roster imports and attendance logging.
   - **PPT & Media**: Support classroom resource uploads and in-browser preview via Spatie MediaLibrary.

4. **Skill Evaluation & Annual Assessment**:
   - Model `Skill` (tunjang/category, code, name, description), `SkillScale` (value, label, color badge), and `SkillEvaluation` (student, cohort, skill, scale, notes, evaluated_at).
   - Port `SkillEvaluationPage` matrix editor for fast end-of-term evaluations per cohort.

## [ADR-006-CONSEQUENCES] Consequences

- **High Speed of Delivery**: Leverages established, debugged SIS UI/UX patterns without trial-and-error design.
- **Architectural Harmony**: Both projects share the same Laravel + Filament + Spatie stack, making models, queries, and Livewire components idiomatic and easy to maintain.
- **Strict Tenant Isolation**: All ported tables and queries are enforced with `school_id` multi-tenant scoping.
