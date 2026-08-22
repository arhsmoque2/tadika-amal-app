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

## [DESIGN-THEME-INTEGRITY] Design System & Token Consistency

> Theme consistency is enforced via design tokens (derived from `@lapidist/design-lint` and `vlmkit` patterns). Raw arbitrary hex colors and random spacing classes are prohibited.

### 1. Color Tokens (Tailwind v4 / Filament Primary Map)
- **Primary / Brand**: `Emerald` (Islamic Kindergarten visual identity)
  - `primary-50`: `#ecfdf5` (tinted card surfaces)
  - `primary-500`: `#10b981` (active tabs, primary buttons)
  - `primary-600`: `#059669` (hover/focus states)
  - `primary-900`: `#064e3b` (primary text accents)
- **Neutrals / Canvas**: `Slate`
  - `surface-canvas`: `slate-50` (`#f8fafc`)
  - `surface-card`: `#ffffff`
  - `border-subtle`: `slate-200` (`#e2e8f0`)
  - `text-main`: `slate-900` (`#0f172a`)
  - `text-muted`: `slate-500` (`#64748b`)
- **Semantic Status**:
  - `Success / Hadir`: `emerald-600` / `emerald-50` chip background
  - `Danger / Tidak Hadir`: `rose-600` / `rose-50` chip background
  - `Warning / Dalam Latihan`: `amber-600` / `amber-50` chip background

### 2. Spacing & Typography Scale
- **Grid Spacing Rhythm**: Strict 4px/8px modular grid (`p-2`, `p-4`, `p-6`, `gap-3`, `gap-4`).
- **Typography Tokens**: Inter / Plus Jakarta Sans font stack:
  - `heading-lg`: `1.25rem` (`text-xl`), font-semibold
  - `body-default`: `0.875rem` (`text-sm`), font-normal
  - `label-dense`: `0.75rem` (`text-xs`), font-medium uppercase tracking-wider

---

## [DESIGN-ATTENDANCE] Daily Attendance Screen Contract

> A rapid-fire roster view designed to be completed in under 60 seconds.

- **Header**: Today's auto-populated date, class selector, count indicator (`X/Y Hadir`).
- **Body**: Dense list/table with student avatar, full name, and single-click toggle chips:
  - `[ Hadir (Emerald) ]` / `[ Tidak Hadir (Rose) ]`
  - Inline expandable text input for absence remarks (e.g., *Cuti sakit*).
- **Touch Target Standard**: Status chips must maintain a minimum bounding box of $44\text{px} \times 44\text{px}$ on mobile/tablet screens.
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
