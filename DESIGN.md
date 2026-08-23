# Design

## [DESIGN-PANEL] Filament Operational Workspace (v1)

> The teacher and admin interface is built with Filament v4 components, customized to provide dense, responsive data-entry, rapid-fire operations, and automated document generation.

- **Theme**: Clean emerald/slate visual identity tailored for Islamic kindergarten operational clarity.
- **Navigation Structure**:
  - 📋 **Kehadiran Harian** (`AttendanceResource` — rapid-fire 60s roster with batch status chips)
  - 🎓 **Pengurusan Murid** (`StudentResource` — custom profile builder cards, photo capture, allergy flags)
  - 🩺 **Kesihatan & Keselamatan** (`HealthRecordResource` & `IncidentLogResource` — morning triage, BMI, allergy management)
  - 📝 **Rekod Pentaksiran** (`AssessmentRecordResource` — KSPK domain schema recorder, append-only history)
  - 📅 **Jadual Waktu** (`TimetableResource` — 5-day blank grid with collision prevention)
  - 💳 **Yuran & Invois** (`FeeInvoiceResource` — payment tracking, tax relief receipts)
  - 📄 **Pusat Dokumen & Eksport** (`DocumentCenter` — DOCX letter generator, PPTX orientation slides, PDF report cards)
  - ⚙️ **Konfigurasi Platform** (`AssessmentSchemaResource` & `ProfileSchemaResource` — admin JSON field builders)

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

> Rapid-fire roster view designed to be completed in under 60 seconds.

- **Header**: Auto-populated today date, cohort selector, realtime tally indicator (`X/Y Hadir`).
- **Body**: Dense list/table with student avatar, full name, and single-click toggle chips:
  - `[ Hadir (Emerald) ]` / `[ Tidak Hadir (Rose) ]`
  - Inline expandable text input for absence remarks (e.g., *Cuti sakit*).
- **Touch Target Standard**: Status chips maintain $\ge 44\text{px} \times 44\text{px}$ touch targets on mobile/tablet screens.
- **Keyboard Navigation**: Full Tab/Shift+Tab navigation across students with `Space`/`Enter` status toggling (no pointer-only traps).
- **Actions**: `Simpan Kehadiran` (Save & Lock).

---

## [DESIGN-ASSESSMENT] Dynamic Assessment & Session Entry

> Follows **ADR-003** & **ADR-004**. Two distinct UI modes:

### 1. Schema Builder (Admin View)
- Built using Filament `Repeater`.
- Enables admin/headmaster to define assessment areas (e.g., *Iqra'*, *Hafazan*, *Matematik*, *KSPK*) and add custom fields (`Text`, `Number`, `Dropdown Select`, `Date`, `Checkbox`).

### 2. Session Recorder (Teacher View)
- Teacher selects student(s) and assessment category.
- Form dynamically renders the fields based on the active JSON schema.
- **Text Collision Invariant**: Dynamic field labels and student names are bounded with automatic wrapping (`break-words`) to prevent layer collisions.
- **Append-Only Action**: `Simpan Rekod Sesi`. Appends a new timestamped row to history with zero mutation of past records.

---

## [DESIGN-TIMETABLE] Blank-Grid Timetable Interface

> Empowers the teacher to configure daily slots without rigid curriculum presets.

- **Layout**: 5-day columns (Isnin – Jumaat).
- **Zero Collapsed Invariant**: Empty time slots render a designated placeholder container ($h \ge 48\text{px}$) to avoid $0\text{px}$ height collapse.
- **Slot Builder**: Addable time blocks with:
  - `Masa Mula` & `Masa Tamat` (Time pickers)
  - `Nama Slot` (Free text: e.g., *Iqra'*, *Rehat*, *Sains*)
  - `Kategori` (Subject, Recess, Assembly, Free Play)
- **Today's Highlight**: Opening the dashboard automatically filters and highlights current day's slots.

---

## [DESIGN-DOCUMENTS-EXPORT] Tri-Format Export & Document Hub

> Follows **ADR-008**. Seamless transition from legacy paperwork to automated reporting.

1. **Universal Import Wizard**: 4-step modal on all tabular views (`Student`, `Teacher`, `Cohort`, `Invoice`) supporting column auto-matching and relationship resolution.
2. **Spreadsheet Exports (`.xlsx`, `.csv`)**: Direct header actions via `pxlrbt/filament-excel` with custom column filtering.
3. **Word Generator (`.docx`)**: Generates official kindergarten letters, fee receipts, and staff appointment contracts via PHPWord template merging.
4. **Presentation Decks (`.pptx`)**: Generates parent orientation slides and weekly lesson decks using brand-aligned slide templates.
5. **Printable Posters & Certificates (`PDF` / `HTML`)**: High-impact printable KSPK term report cards and parent gate-pass verification cards.
