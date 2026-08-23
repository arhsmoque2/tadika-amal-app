# AGENTS.md — Tadika Amal Apps Operational & Quality Standards

> **Role**: Autonomous, Compliant, Quality-Guarded Engineering Agent / Developer.
> **Objective**: Drive preschool operations software with zero runtime regressions, verified mobile ergonomics, statutory LHDN/JKM compliance, and continuous error harvesting.

---

## 1. Prime Directives & Invariants

1. **The Core Invariant**: *"The platform provides containers, the school provides content."*
   - Never hardcode rigid curriculum subjects (*Iqra'*, *Hafazan*, *Solat*, *Math*) as fixed database table columns. They are configured dynamically at runtime via the KSPK skill schema engine ([`ADR-003`](adr/ADR-003-json-schema-for-runtime-configurable-fields.md)).
2. **Strict Multi-Tenancy Scoping**:
   - Every Eloquent model, database migration, and query MUST enforce `school_id` foreign key isolation and composite unique constraints to prevent cross-school data pollution.
3. **Statutory Compliance Readiness**:
   - Preschool fee receipts must carry the official Malaysian Income Tax Section 46(1)(r) tax relief declaration and computerized serial format (`REC-YYYYMM-XXXX`).
   - Accident and injury tracking must comply with Jabatan Kebajikan Masyarakat (JKM) logbook standards.
4. **Continuous Error & Fix Harvesting**:
   - Any resolved syntax error, layout collision, submodule trap, secret leak, or compliance gap **MUST be recorded immediately** in [`errors-fixes.md`](errors-fixes.md).
5. **No Unverified "Passed" Claims**:
   - Never mark a check, gate, or review item as passed, resolved, or compliant in a PR description, commit message, or `errors-fixes.md` unless you actually ran the real command and observed the real output — not "wrote a script that should catch this," but "ran `X` and saw `Y`."
   - A static file-content scan (grep-for-a-pattern, "does this file exist," "does this JSON key match") is a legitimate and useful gate, but it is **not a substitute** for actually running `composer install`, `pnpm run build`, `php artisan migrate`, `php artisan test`, or `pint --test`. State which kind of check you ran when you report a result.
   - **Why this is a hard rule, not a style preference**: PR #2's `errors-fixes.md` (ERR-006, ERR-011, ERR-015, ERR-022) claimed zero syntax errors, Pint compliance, and 6 passing feature tests — all self-reported without the pipeline ever actually executing. When CI was made to actually run `composer validate`, `php artisan migrate`, `pint --test`, and `php artisan test` for real, it found: a stale `composer.lock` missing two required packages, a PHP fatal from a property-type invariance violation on 9 Filament classes, an orphaned migration with an internal duplicate-column bug, 27 real Pint violations across 25 files, and a feature-test suite where every single test failed on first real run (wrong column names, invalid enum values, views rendered with no data). See `errors-fixes.md` ERR-023 through ERR-028 for the full detail. None of this was exotic — it was caught by the most basic possible action: running the command and reading the output.

---

## 2. Standard Practice & Workflow Loop

Every agent or developer working in this repository must follow this 4-step practice:

```
┌─────────────────┐     ┌──────────────────────┐     ┌─────────────────────┐     ┌──────────────────────┐
│ 1. Implement    │ ──> │ 2. Run Quality Gates │ ──> │ 3. Harvest Fixes    │ ──> │ 4. Commit & Handoff  │
│ Feature / Fix   │     │ (pnpm qa:all + Pint) │     │ into errors-fixes.md│     │ with clean receipts  │
└─────────────────┘     └──────────────────────┘     └─────────────────────┘     └──────────────────────┘
```

### Step 1: Implementation Discipline
- Maintain mobile ergonomics: interactive status pills, attendance toggles, and screening controls must maintain $\ge 44\text{px} \times 44\text{px}$ touch targets.
- Ensure all Blade directives (`@if`, `@forelse`, `@can`) are symmetrically closed.
- Restrict colors to the official `Emerald` / `Slate` / `Rose` / `Amber` semantic design tokens.

### Step 2: Quality Gate Execution (Mandatory Before Commit)
Run the automated verification suite before declaring any task complete:

```powershell
# 1. Run full JavaScript, UI/UX, Secret & A11y Doctor
pnpm doctor

# 2. Run Domain UI/UX & Blade Integrity Gate
pnpm run qa:ui

# 3. Run PHP Syntax Check (Recursive)
Get-ChildItem -Path app,database -Filter *.php -Recurse | ForEach-Object { $res = php -l $_.FullName 2>&1; if ($res -notmatch "No syntax errors detected") { Write-Host $res } }

# 4. Run Laravel Pint (PSR-12 & Clean Alpha Import Sorting)
composer pint
```

### Step 3: Error & Fix Recording Protocol
Whenever an unexpected error or lint breakage is caught:
1. Open [`errors-fixes.md`](errors-fixes.md).
2. Append a new record to `[ERR-MATRIX]` containing:
   - **ID**: `ERR-XXX` (sequential).
   - **Issue / Risk Description**: What failed or could have failed in production.
   - **Detection Engine / Script**: The exact CLI tool (e.g. `Oxlint`, `secret-scanner.mjs`, `_qa/tadika-ui-ux-quality-gate.mjs`, `php -l`, `pint`).
   - **Fix Type**: `Agent-Manual` or `Checker-Automated`.
   - **Root Cause & Permanent Fix**: Technical rationale and remediation.
3. Add a subsection under `[ERR-DETAIL]` if the issue involves architectural lessons.

### Step 4: Clean Commit & Handoff
- Check `git status` to ensure zero loose temporary files or untracked `.git` folders.
- Update [`HANDOFF.md`](HANDOFF.md) with verified state and exact test receipts.

---

## 3. DevTool Capabilities & Scripts Inventory

| Script / Command | Toolchain / Engine | Primary Capability & Purpose |
| :--- | :--- | :--- |
| `pnpm doctor` | `_AGENT-CAPABILITIES/arh-js-devkit/` | Runs master doctor: Oxlint (<20ms), Secret Scanner, Schema Validator, Layout/A11y Gate, Ratchet, and ARH Docs integrity. |
| `pnpm run qa:ui` | `_qa/tadika-ui-ux-quality-gate.mjs` | Audits Blade directive balance, mobile touch bounding boxes ($\ge 44\text{px}$), design token contrast, and statutory LHDN/JKM legal text. |
| `pnpm run qa:all` | Node.js / Pnpm | Executes `pnpm doctor` and `pnpm run qa:ui` sequentially in a single pass. |
| `pnpm run docs:check` | `bin/arh-docs-doctor.mjs` | Validates presence and non-empty status of the 7 canonical ARH documentation suite files. |
| `composer doctor` | Node.js / Composer | Composer proxy executing the master JS/UI doctor. |
| `composer pint` | Laravel Pint (`pint.json`) | Enforces strict PSR-12 code style, short array syntax, and alphabetical `use` statement sorting. |
| `composer phpstan` | Larastan (`phpstan.neon`) | Static analysis at Level 5 with Octane compatibility and Eloquent model property inspection. |
| `npx knip` | Knip (`knip.json`) | Scans Vite bundles against `resources/` to eliminate dead code and orphan npm dependencies. |
| `npx lighthouserc` | Google Lighthouse (`lighthouserc.json`) | Asserts performance ($\ge 0.85$), accessibility ($\ge 0.95$), and PWA offline baseline. |
| `php -l` (Recursive) | PHP 8.4 Engine CLI | High-speed syntax linting across all models, migrations, and Livewire classes. |

---

## 4. Documentation Suite Navigation

Every change must maintain synchronization across canonical documentation:
- [`README.md`](README.md): Operational overview and status matrix.
- [`INTENT.md`](INTENT.md): System boundary and platform principles.
- [`ARCHITECTURE.md`](ARCHITECTURE.md): Data schemas, multi-tenancy, and security invariants.
- [`DEVTOOLS.md`](DEVTOOLS.md): Pinned versions, environment setup, and CLI commands.
- [`QUALITY-GATES.md`](QUALITY-GATES.md): Multi-tier acceptance thresholds.
- [`errors-fixes.md`](errors-fixes.md): The live quality audit and remediation log.
- [`HANDOFF.md`](HANDOFF.md): Real-time verified milestone continuity.
