# Quality Gates

## [QG-LOCAL] Local Transition Gate

> A feature slice or pull request may only be handed off when all three verification tiers pass.

```text
PASS: vendor/bin/pint --test
PASS: php artisan test
PASS: node D:/_ARH-AGENT-OS/_AGENT-CAPABILITIES/arh-js-devkit/bin/arh-js-doctor.mjs .
```

---

## [QG-TIER-1] Tier 1: Static Code, Security & Design Token Gate (<1s)

> Fast static analysis running prior to commit or push:

1. **PHP Styling & Static Types**: Laravel Pint (`vendor/bin/pint --test`) enforcing strict PSR-12 conventions.
2. **Design Token Conformance (`@lapidist/design-lint`)**:
   - Zero raw arbitrary hex colors in Blade/Filament templates (e.g. no `#123456`). All colors must map to the defined `Emerald` / `Slate` palette.
   - Spacing rhythm restricted to the 4px/8px modular grid (`p-2`, `p-4`, `p-6`, `gap-3`, `gap-4`).
3. **Secret Scanner**: Real-time scanning ensuring 0 exposed tokens, credentials, or private keys (`secret-scanner.mjs`).

---

## [QG-TIER-2] Tier 2: Headless DOM, Layout & Geometry Math Gate (<5s)

> Deterministic DOM & geometry calculations derived from `vlmkit` and `layout-lint-mcp`:

1. **Viewport Matrix Coverage**:
   - **Mobile Portrait**: `390px x 844px` (touch target minimum $\ge 44\text{px}$)
   - **Mobile Landscape**: `844px x 390px` (zero vertical lockouts or swallowed controls)
   - **Tablet Portrait**: `768px x 1024px` (dense table readability & collapsible navigation)
   - **Tablet Landscape**: `1024px x 768px`
   - **Desktop**: `1440px x 900px` (dense wide data grid)

2. **DOM Geometry Invariants (`layout-integrity-gate.mjs`)**:
   - **Zero Swallowed Elements**: No child content clipped invisibly by parent `overflow: hidden` (`scrollWidth > clientWidth`).
   - **Zero Page Spills**: `document.documentElement.scrollWidth <= window.innerWidth` across all viewports.
   - **Text-Collision & Protrusion Trap**: Same-layer text elements must never overlap (guards dynamic assessment labels and student names).
   - **Sticky & Scroll Bounds**: Sticky headers (e.g., student roster column) must remain pinned without occluding scrollable rows beneath.
   - **Zero Collapsed Containers**: No timetable slots or card containers may collapse to $0\text{px}$ height.
   - **Touch Target Area**: Interactive buttons and status chips must maintain $\ge 44\text{px} \times 44\text{px}$ touch target bounds on mobile/tablet.

---

## [QG-TIER-3] Tier 3: Behavioral, Interaction & Performance Ratchet (CI / Pre-Release)

> Interactive operability, internationalization robustness, and regression guards:

1. **Keyboard Operability & Focus Order (`vlmkit check interactions`)**:
   - **Zero Pointer-Only Controls**: Clickable elements must be native `<button>`, `<a>`, or have `role="button"` + `tabindex="0"` with `Enter`/`Space` handlers.
   - **Focus Rings**: Visual focus indicators must never be suppressed (`outline: none` without visible replacement).
   - **Element Occlusion Trap**: Floating overlays/modals must not intercept clicks intended for background table controls (`elementFromPoint` audit).
2. **String Expansion Stress Test (`stress i18n`)**:
   - Layouts must survive $+30\%$ string length expansion without truncation or card breakage, ensuring Malay language labels (*e.g., "Rancangan Pengajaran Harian"*) wrap gracefully.
3. **WCAG Contrast & Theme Parity (`check theme`)**:
   - Text on status chips (Emerald/Rose/Amber) must meet minimum $4.5:1$ contrast ratio across both light and dark backgrounds.
4. **Performance Benchmark & Quality Ratchet (`benchmark-verify.mjs` & `ratchet-gate.mjs`)**:
   - Dynamic schema compilation must pass median-of-trials benchmark testing ($N \ge 100$ iterations) with P95 outlier guards.
   - Total gzip asset weights are tracked against `.arh-quality-gate.json` baseline with zero allowance for uncompressed bloat.

---

## [QG-DOCS] Documentation Conformance Gate

> Every architectural modification must maintain consistency across `INTENT.md`, `ARCHITECTURE.md`, `DESIGN.md`, `IMPLEMENTATION.md`, `DEVTOOLS.md`, `CHANGELOG.md`, and `HANDOFF.md`.
