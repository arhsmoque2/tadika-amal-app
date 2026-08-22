# Quality Gates

## [QG-LOCAL] Local Transition Gate

> A feature slice or pull request may only be handed off when database migrations, Pint code style, Pest automated tests, and the ARH DevKit Quality Doctor pass.

```text
PASS: vendor/bin/pint --test
PASS: php artisan test
PASS: node D:/_ARH-AGENT-OS/_AGENT-CAPABILITIES/arh-js-devkit/bin/arh-js-doctor.mjs .
```

---

## [QG-THEME-INTEGRITY] Design Token & Theme Consistency Gate

> Inspired by `@lapidist/design-lint` and `uimatch` patterns in `ui-ux-integrity-devkits`:

1. **Design Token Conformance**:
   - Zero raw arbitrary hex colors in templates or components (e.g. no hardcoded `#123456`). All colors must map to the defined `Emerald` / `Slate` token palette.
   - Spacing must strictly adhere to the 4px/8px modular rhythm (`p-2`, `p-4`, `p-6`, `gap-3`, `gap-4`).
2. **Visual Consistency (vlmkit / uimatch reference)**:
   - Status indicators across Attendance and Assessment must use standardized semantic tokens (`emerald` for Success/Hadir, `rose` for Danger/Tidak Hadir, `amber` for Warning/Pending).

---

## [QG-LAYOUT] Layout Integrity & Viewport Matrix

> The user interface must maintain visual integrity across the standard multi-device matrix:

1. **Viewport Matrix**:
   - **Mobile Portrait**: `390px x 844px` (touch target minimum $\ge 44\text{px}$)
   - **Mobile Landscape**: `844px x 390px` (zero vertical lockouts / swallowed controls)
   - **Tablet Portrait**: `768px x 1024px` (dense table readability & collapsible navigation)
   - **Tablet Landscape**: `1024px x 768px`
   - **Desktop**: `1440px x 900px` (dense wide data grid)

2. **Integrity Invariants (`layout-integrity-gate.mjs`)**:
   - **Zero Swallowed Elements**: No child content clipped invisibly by parent `overflow: hidden`.
   - **Zero Page Spills**: `document.documentElement.scrollWidth <= window.innerWidth` across all viewports.
   - **Touch Target Standard**: Minimum $44\text{px} \times 44\text{px}$ bounding box for all mobile tap targets.

---

## [QG-PERFORMANCE] Performance Profiling & Asset Ratchet

> Performance regressions are prevented using the ARH DevKit statistical profiler and quality ratchet:

1. **Benchmark Speedup Verifier (`benchmark-verify.mjs`)**:
   - Critical queries and dynamic schema compilation must pass median-of-trials benchmark testing ($N \ge 100$ iterations) with P95 outlier guards.
2. **Quality Ratchet (`ratchet-gate.mjs`)**:
   - Asset bundle sizes are tracked against committed baselines (`.arh-quality-gate.json`).
   - Zero tolerance for uncompressed bundle bloat or runaway bundle size increases.

---

## [QG-DESIGN-LINT] Design & Accessibility Linting

> Frontends and Blade/Filament templates must pass:

1. **Accessibility**: Mandatory `<meta name="viewport">`, valid HTML5 semantic tags, and explicit `alt` attributes on image uploads (`a11y-verifier.mjs`).
2. **Static Layout Linting**: Prohibition of dangerous CSS combinations (e.g. `100vw` with horizontal padding, unbounded negative margins).
3. **Secret Scanner**: Real-time scanning for exposed API tokens or sensitive credentials (`secret-scanner.mjs`).

---

## [QG-DOCS] Documentation Conformance Gate

> Every architectural modification must maintain consistency across `INTENT.md`, `ARCHITECTURE.md`, `DESIGN.md`, `IMPLEMENTATION.md`, `DEVTOOLS.md`, `CHANGELOG.md`, and `HANDOFF.md`.
