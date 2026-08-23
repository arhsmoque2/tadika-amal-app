#!/usr/bin/env node
/**
 * Tadika Amal Apps — Canonical Documentation Quality Doctor (v2.0)
 * Validates presence, Separation of Concerns (SoC), Declared-vs-Built conformance,
 * ADR lifecycle headers, and failure capsule standards.
 * Hermetic, zero host-dependency runner for CI and Cloud Sandboxes.
 */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const repoRoot = path.resolve(__dirname, '..');

console.log('\n===============================================================');
console.log('  📚 [TADIKA AMAL DOCS DOCTOR] ARH Docs & SoC Quality Gate');
console.log('===============================================================\n');

let totalErrors = 0;
let totalWarnings = 0;

// 1. Core Documentation Audit
const requiredDocs = [
    { name: 'README.md', minLines: 30 },
    { name: 'ARCHITECTURE.md', minLines: 40 },
    { name: 'CHANGELOG.md', minLines: 20 },
    { name: 'HANDOFF.md', minLines: 20 },
    { name: 'GOTCHAS.md', minLines: 20 },
    { name: 'RECIPES.md', minLines: 20 },
    { name: 'AGENTS.md', minLines: 30 }
];

console.log('🔍 Gate 1: Checking 7 Core ARH Canonical Documentation Files...');
for (const doc of requiredDocs) {
    const docPath = path.join(repoRoot, doc.name);
    if (!fs.existsSync(docPath)) {
        console.error(`  ❌ Missing mandatory document: ${doc.name}`);
        totalErrors++;
        continue;
    }

    const content = fs.readFileSync(docPath, 'utf8');
    const lines = content.split('\n').filter(l => l.trim().length > 0);
    if (lines.length < doc.minLines) {
        console.error(`  ❌ ${doc.name} appears hollow (${lines.length} non-empty lines, minimum is ${doc.minLines})`);
        totalErrors++;
    } else {
        console.log(`  ✅ ${doc.name} (${lines.length} lines verified)`);
    }
}

// 2. Separation of Concerns (SoC): INTENT.md Stack Leak Guard
console.log('\n🧠 Gate 2: Separation of Concerns — INTENT.md Stack Isolation...');
const intentPath = path.join(repoRoot, 'INTENT.md');
if (fs.existsSync(intentPath)) {
    const text = fs.readFileSync(intentPath, 'utf8');
    const STACK_PATTERNS = [
        { pattern: /##\s+Tech\s+Stack/i, desc: 'explicit Tech Stack heading' },
        { pattern: /\b(?:Laravel\s+1\d|PHP\s+8\.\d|FastAPI|Vue\.js|React\s+1\d|TailwindCSS|PostgreSQL|SQLite|Pest|Pytest)\b/i, desc: 'framework/technology stack declaration' },
        { pattern: /\b(?:pnpm|composer|pip|uv\s+add|cargo\s+add)\b/i, desc: 'package manager dependency command' }
    ];

    for (const sp of STACK_PATTERNS) {
        if (sp.pattern.test(text)) {
            console.error(`  ❌ [SoC Violation] INTENT.md declares ${sp.desc}. INTENT.md is strictly for problem/user intent; move tech stack to STACK.md or ARCHITECTURE.md.`);
            totalErrors++;
        }
    }
    if (totalErrors === 0) {
        console.log('  ✅ INTENT.md is purely focused on user problem & business intent (0 stack leaks).');
    }
}

// 3. Separation of Concerns (SoC): CAPABILITIES.md Operational Script Leak Guard
console.log('\n⚙️ Gate 3: Separation of Concerns — CAPABILITIES.md Script Isolation...');
const capDocPath = path.join(repoRoot, 'CAPABILITIES.md');
if (fs.existsSync(capDocPath)) {
    const text = fs.readFileSync(capDocPath, 'utf8');
    const SCRIPT_PATTERNS = [
        { pattern: /```(?:bash|sh|powershell|pwsh)\s*\n\s*(?:npm\s+run|pnpm\s+run|php\s+artisan|composer\s+run|uv\s+run|python\s+scripts\/)/i, desc: 'operational developer script execution block' },
        { pattern: /\bapp\s+must\s+enable\s+user\s+to\s+use\s+specific\s+script\b/i, desc: 'script execution requirement' }
    ];

    for (const sp of SCRIPT_PATTERNS) {
        if (sp.pattern.test(text)) {
            console.error(`  ❌ [SoC Violation] CAPABILITIES.md contains ${sp.desc}. CAPABILITIES.md is strictly for system domain capabilities; move developer scripts to RECIPES.md or DEVTOOLS.md.`);
            totalErrors++;
        }
    }
    if (totalErrors === 0) {
        console.log('  ✅ CAPABILITIES.md is purely focused on domain capabilities (0 script leaks).');
    }
}

// 4. Declared-vs-Built Conformance (capabilities.json vs Code/ADRs)
console.log('\n📦 Gate 4: Declared-vs-Built Conformance (capabilities.json)...');
const capManifestPath = path.join(repoRoot, 'capabilities.json');
if (!fs.existsSync(capManifestPath)) {
    console.error('  ❌ Missing root capabilities.json specification manifest.');
    totalErrors++;
} else {
    try {
        const manifest = JSON.parse(fs.readFileSync(capManifestPath, 'utf8'));
        const modules = manifest.capabilities?.modules || [];
        const legalInvariants = manifest.capabilities?.legal_invariants || [];
        console.log(`  ✅ capabilities.json verified (${modules.length} operational modules, ${legalInvariants.length} legal invariants declared).`);

        // Verify legal invariant target files exist
        for (const inv of legalInvariants) {
            const targetPath = path.join(repoRoot, inv.target_file);
            if (!fs.existsSync(targetPath)) {
                console.error(`  ❌ Legal invariant target file missing: ${inv.target_file} (${inv.id})`);
                totalErrors++;
            }
        }
    } catch (e) {
        console.error(`  ❌ capabilities.json JSON parse error: ${e.message}`);
        totalErrors++;
    }
}

// 5. Architecture Decision Records (ADRs) & Status Headers
console.log('\n📜 Gate 5: Architecture Decision Records (ADRs)...');
const adrDir = path.join(repoRoot, 'adr');
if (!fs.existsSync(adrDir)) {
    console.error('  ❌ Missing adr/ directory');
    totalErrors++;
} else {
    const adrFiles = fs.readdirSync(adrDir).filter(f => f.endsWith('.md'));
    if (adrFiles.length < 6) {
        console.error(`  ❌ Expected at least 6 ADR records in adr/, found ${adrFiles.length}`);
        totalErrors++;
    } else {
        for (const adrFile of adrFiles) {
            const fullPath = path.join(adrDir, adrFile);
            const content = fs.readFileSync(fullPath, 'utf8');
            if (!content.includes('**Status**:') && !content.includes('Status:')) {
                console.error(`  ❌ ADR missing valid Status header: ${adrFile}`);
                totalErrors++;
            }
        }
        if (totalErrors === 0) {
            console.log(`  ✅ Verified ${adrFiles.length} ADR decision records with valid status headers.`);
        }
    }
}

// 6. GOTCHAS Capsule Structure
console.log('\n💊 Gate 6: Failure Capsule Standard Invariant...');
const gotchasPath = path.join(repoRoot, 'GOTCHAS.md');
if (fs.existsSync(gotchasPath)) {
    const content = fs.readFileSync(gotchasPath, 'utf8');
    const hasSymptom = /Symptom/i.test(content);
    const hasRootCause = /Root\s+Cause/i.test(content);
    const hasFix = /Fix|Resolution/i.test(content);
    if (!hasSymptom || !hasRootCause || !hasFix) {
        console.warn('  ⚠️ GOTCHAS.md should follow standard capsule format (Symptom -> Root Cause -> Permanent Fix -> Verification)');
        totalWarnings++;
    } else {
        console.log('  ✅ GOTCHAS.md follows standard 4-phase failure capsule structure.');
    }
}

console.log('\n===============================================================');
if (totalErrors === 0) {
    console.log(`🎉 [SUCCESS] All documentation & SoC quality gates passed! (${totalWarnings} warnings)\n`);
    process.exit(0);
} else {
    console.error(`❌ [FAILED] Documentation gate failed with ${totalErrors} errors.\n`);
    process.exit(1);
}
