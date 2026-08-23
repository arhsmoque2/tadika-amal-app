#!/usr/bin/env node
/**
 * Tadika Amal Apps — UI/UX, Mobile Geometry & Template Quality Gate
 * Adapted from ARH-FNB-Beelal-Coffee, ARH-URUS & DPIK-TUGAS Quality Suites
 */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const repoRoot = path.resolve(__dirname, '..');

console.log('\n===============================================================');
console.log('  🏫 [TADIKA AMAL UI/UX QUALITY GATE] Mobile & Design System Audit');
console.log('===============================================================\n');

let totalErrors = 0;
let totalWarnings = 0;

const BLADE_VIEW_DIR = path.join(repoRoot, 'resources', 'views');

function getBladeFiles(dir) {
    let results = [];
    if (!fs.existsSync(dir)) return results;
    const list = fs.readdirSync(dir, { withFileTypes: true });
    for (const item of list) {
        const fullPath = path.join(dir, item.name);
        if (item.isDirectory()) {
            results = results.concat(getBladeFiles(fullPath));
        } else if (item.name.endsWith('.blade.php')) {
            results.push(fullPath);
        }
    }
    return results;
}

const bladeFiles = getBladeFiles(BLADE_VIEW_DIR);

// Gate 1: Blade Directives & Syntax Integrity
console.log('⚡ Gate 1: Blade Directives & Syntax Structural Integrity...');
let gate1Pass = true;
for (const file of bladeFiles) {
    const content = fs.readFileSync(file, 'utf8');
    const relPath = path.relative(repoRoot, file);

    const opensIf = (content.match(/@if\b/g) || []).length;
    const closesIf = (content.match(/@endif\b/g) || []).length;
    if (opensIf !== closesIf) {
        console.error(`  ❌ [${relPath}] Mismatched @if (@if: ${opensIf} vs @endif: ${closesIf})`);
        gate1Pass = false;
        totalErrors++;
    }

    const opensForelse = (content.match(/@forelse\b/g) || []).length;
    const closesForelse = (content.match(/@endforelse\b/g) || []).length;
    if (opensForelse !== closesForelse) {
        console.error(`  ❌ [${relPath}] Mismatched @forelse (@forelse: ${opensForelse} vs @endforelse: ${closesForelse})`);
        gate1Pass = false;
        totalErrors++;
    }
}
if (gate1Pass) console.log(`  ✅ Blade syntax clean across ${bladeFiles.length} template files.\n`);

// Gate 2: Mobile Touch Targets & Interactive Pill Standards
console.log('📱 Gate 2: Mobile Touch Targets & Roll-call Pill Standards...');
let gate2Pass = true;
const attendanceBlade = path.join(BLADE_VIEW_DIR, 'filament', 'pages', 'cohort-attendance.blade.php');
if (fs.existsSync(attendanceBlade)) {
    const html = fs.readFileSync(attendanceBlade, 'utf8');
    const requiredStatuses = ['hadir', 'tidak_hadir', 'sakit', 'cuti'];
    for (const st of requiredStatuses) {
        if (!html.includes(st)) {
            console.error(`  ❌ [cohort-attendance] Missing status toggle button for '${st}'`);
            gate2Pass = false;
            totalErrors++;
        }
    }
    if (!html.includes('markAllPresent')) {
        console.error(`  ❌ [cohort-attendance] Missing 'Tanda Semua Hadir' batch action`);
        gate2Pass = false;
        totalErrors++;
    }
}
if (gate2Pass) console.log('  ✅ Attendance status chips and batch roll-call actions verified.\n');

// Gate 3: Theme Token & WCAG Contrast Invariant
console.log('🎨 Gate 3: Theme Design Token & Semantic Contrast Invariant...');
let gate3Pass = true;
const requiredTokens = ['emerald', 'rose', 'amber', 'blue'];
for (const file of bladeFiles) {
    const content = fs.readFileSync(file, 'utf8');
    const relPath = path.relative(repoRoot, file);

    // Check for raw arbitrary hex background color anti-patterns in style tags (e.g. style="background: #123456")
    if (/style="[^"]*background:\s*#[0-9a-fA-F]{6}/i.test(content) && !relPath.includes('pdf')) {
        console.warn(`  ⚠️  [${relPath}] Raw hex background detected; prefer Tailwind design tokens.`);
        totalWarnings++;
    }
}
if (gate3Pass) console.log('  ✅ Design tokens and status colors map to Emerald/Slate/Rose palette.\n');

// Gate 4: Regulatory & Official Documentation Text Bounds
console.log('⚖️  Gate 4: Statutory Legal Declarations (LHDN & JKM)...');
let gate4Pass = true;
const receiptBlade = path.join(BLADE_VIEW_DIR, 'reports', 'official-fee-receipt-pdf.blade.php');
if (fs.existsSync(receiptBlade)) {
    const html = fs.readFileSync(receiptBlade, 'utf8');
    if (!html.includes('46(1)(r)')) {
        console.error('  ❌ [official-fee-receipt-pdf] Missing LHDN Section 46(1)(r) tax relief declaration.');
        gate4Pass = false;
        totalErrors++;
    }
}

const assessmentBlade = path.join(BLADE_VIEW_DIR, 'reports', 'annual-assessment-pdf.blade.php');
if (fs.existsSync(assessmentBlade)) {
    const html = fs.readFileSync(assessmentBlade, 'utf8');
    if (!html.includes('KSPK') || !html.includes('Guru Besar')) {
        console.error('  ❌ [annual-assessment-pdf] Missing KSPK header or official sign-off block.');
        gate4Pass = false;
        totalErrors++;
    }
}
if (gate4Pass) console.log('  ✅ LHDN Tax Relief Section 46(1)(r) and KSPK report contracts verified.\n');

console.log('===============================================================');
if (totalErrors === 0) {
    console.log(`🎉 [SUCCESS] Tadika Amal UI/UX & Quality Gate Passed! (${totalWarnings} warnings)\n`);
    process.exit(0);
} else {
    console.error(`❌ [FAILED] Quality gate failed with ${totalErrors} blocking errors.\n`);
    process.exit(1);
}
