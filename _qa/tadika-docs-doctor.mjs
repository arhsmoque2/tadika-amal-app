#!/usr/bin/env node
/**
 * Tadika Amal Apps — Canonical Documentation Quality Doctor
 * Validates presence, structure, and completeness of required ARH documentation.
 * Hermetic, zero host-dependency runner for CI and Cloud Sandboxes.
 */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const repoRoot = path.resolve(__dirname, '..');

console.log('\n===============================================================');
console.log('  📚 [TADIKA AMAL DOCS DOCTOR] ARH Documentation Quality Gate');
console.log('===============================================================\n');

let totalErrors = 0;

const requiredDocs = [
    { name: 'README.md', minLines: 30 },
    { name: 'ARCHITECTURE.md', minLines: 40 },
    { name: 'CHANGELOG.md', minLines: 20 },
    { name: 'HANDOFF.md', minLines: 20 },
    { name: 'GOTCHAS.md', minLines: 20 },
    { name: 'RECIPES.md', minLines: 20 },
    { name: 'AGENTS.md', minLines: 30 }
];

console.log('🔍 Checking 7 Core ARH Canonical Documentation Files...');
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

console.log('\n🔍 Checking Architecture Decision Records (ADRs)...');
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
        console.log(`  ✅ Verified ${adrFiles.length} ADR decision records in adr/`);
    }
}

console.log('\n===============================================================');
if (totalErrors === 0) {
    console.log('🎉 [SUCCESS] All documentation quality gates passed!\n');
    process.exit(0);
} else {
    console.error(`❌ [FAILED] Documentation gate failed with ${totalErrors} errors.\n`);
    process.exit(1);
}
