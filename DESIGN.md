# Design

## [DESIGN-PANEL] Filament Operational Workspace (v1)

> The teacher and admin interface is built entirely with Filament v4 components, customized to provide dense, efficient data-entry and query views.

- **Theme**: Clean emerald/slate aesthetic tailored for educational clarity.
- **Navigation**: Sidebar with scoped resources:
  - 📋 **Kehadiran Harian** (Daily Attendance Sheet)
  - 🎓 **Senarai Murid** (Student Registry & Profiles)
  - 📝 **Rekod Pentaksiran** (Assessment Recording & History)
  - 📅 **Jadual Waktu** (Timetable Grid)
  - ⚙️ **Konfigurasi Platform** (Admin Schema & Cohort Builders)

---

## [DESIGN-ATTENDANCE] Daily Attendance Screen Contract

> A rapid-fire roster view designed to be completed in under 60 seconds.

- **Header**: Today's auto-populated date, class selector, count indicator (`X/Y Hadir`).
- **Body**: Dense list/table with student avatar, full name, and single-click toggle chips:
  - `[ Hadir (Green) ]` / `[ Tidak Hadir (Red) ]`
  - Inline expandable text input for absence remarks (e.g., *Cuti sakit*).
- **Actions**: `Simpan Kehadiran` (Save & Lock).

---

## [DESIGN-ASSESSMENT] Dynamic Assessment & Session Entry

> Follows **ADR-003** & **ADR-004**. Two distinct UI modes:

### 1. Schema Builder (Admin View)
- Built using Filament `Repeater`.
- Enables admin/headmaster to define assessment areas (e.g., *Iqra'*, *Hafazan*, *Matematik*) and add custom fields (`Text`, `Number`, `Dropdown Select`, `Date`, `Checkbox`).

### 2. Session Recorder (Teacher View)
- Teacher picks student(s) and assessment category.
- Form dynamically renders the fields based on the active JSON schema.
- Action: `Simpan Rekod Sesi`. Appends a new timestamped row to history with zero mutation of past records.

---

## [DESIGN-TIMETABLE] Blank-Grid Timetable Interface

> Empowers the teacher to configure daily slots without rigid curriculum presets.

- **Layout**: 5-day columns (Isnin – Jumaat).
- **Slot Builder**: Addable time blocks with:
  - `Masa Mula` & `Masa Tamat` (Time pickers)
  - `Nama Slot` (Free text: e.g., *Iqra'*, *Rehat*, *Sains*)
  - `Kategori` (Subject, Recess, Assembly, Free Play)
- **Today's Dashboard**: Opening the dashboard on a Wednesday automatically filters and highlights Wednesday's slots.
