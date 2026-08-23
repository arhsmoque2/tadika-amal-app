#!/usr/bin/env node
/**
 * Tadika Amal Apps — Quality Doctor & Secret Scanner
 * Checks JSON configurations, syntax, and performs hermetic secret scanning.
 * Zero host-dependency runner for local, CI, and cloud sandboxes.
 */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const repoRoot = path.resolve(__dirname, '..');

console.log('\n===============================================================');
console.log('  🩺 [TADIKA AMAL QUALITY DOCTOR] Config & Secret Inspection');
console.log('===============================================================\n');

let totalErrors = 0;
let totalWarnings = 0;

// 1. JSON Configuration Validation
console.log('📋 Gate 1: JSON Configuration Validation...');
const jsonFiles = [
    'package.json',
    'composer.json',
    'pint.json',
    '.prettierrc.json',
    'knip.json',
    'lighthouserc.json'
];

for (const relFile of jsonFiles) {
    const filePath = path.join(repoRoot, relFile);
    if (!fs.existsSync(filePath)) {
        console.warn(`  ⚠️  Optional config file ${relFile} not present`);
        continue;
    }
    try {
        const raw = fs.readFileSync(filePath, 'utf8');
        JSON.parse(raw);
        console.log(`  ✅ ${relFile} valid JSON`);
    } catch (err) {
        console.error(`  ❌ ${relFile} JSON parse error: ${err.message}`);
        totalErrors++;
    }
}

// 2. Secret and Token Leak Scanning
console.log('\n🔒 Gate 2: Secret & Sensitive Token Scanner...');
const SECRET_PATTERNS = [
    { name: 'AWS Access Key ID', regex: /(?:A3T[A-Z0-9]|AKIA|AGPA|AIDA|AROA|AIPA|ANPA|ANVA|ASIA)[A-Z0-9]{16}/ },
    { name: 'GitHub Personal Access Token', regex: /ghp_[a-zA-Z0-9]{36}/ },
    { name: 'Generic Private Key Header', regex: /-----BEGIN (?:RSA |EC |OPENSSH )?PRIVATE KEY-----/ },
    { name: 'OpenAI/Anthropic API Key Pattern', regex: /sk-(?:live-)?[a-zA-Z0-9]{32,}/ }
];

const IGNORE_DIRS = new Set(['.git', 'node_modules', 'vendor', 'storage', '.pyscn']);

function scanDirectoryForSecrets(dir) {
    if (!fs.existsSync(dir)) return;
    const entries = fs.readdirSync(dir, { withFileTypes: true });
    for (const entry of entries) {
        if (IGNORE_DIRS.has(entry.name)) continue;
        const fullPath = path.join(dir, entry.name);
        if (entry.isDirectory()) {
            scanDirectoryForSecrets(fullPath);
        } else if (entry.isFile()) {
            // Avoid binary / lock files for deep regex scan
            if (entry.name.endsWith('.lock') || entry.name.endsWith('.woff2') || entry.name.endsWith('.png') || entry.name.endsWith('.jpg') || entry.name.endsWith('.ico')) {
                continue;
            }
            const content = fs.readFileSync(fullPath, 'utf8');
            for (const pattern of SECRET_PATTERNS) {
                if (pattern.regex.test(content)) {
                    // Check if in documentation / example context
                    if (content.includes('example') || content.includes('placeholder') || entry.name.includes('.example')) {
                        continue;
                    }
                    console.error(`  ❌ [${path.relative(repoRoot, fullPath)}] Found potential secret match for: ${pattern.name}`);
                    totalErrors++;
                }
            }
        }
    }
}

scanDirectoryForSecrets(repoRoot);
if (totalErrors === 0) {
    console.log('  ✅ No unmasked sensitive secrets found across source files.');
}

console.log('\n===============================================================');
if (totalErrors === 0) {
    console.log(`🎉 [SUCCESS] Quality doctor inspection passed! (${totalWarnings} warnings)\n`);
    process.exit(0);
} else {
    console.error(`❌ [FAILED] Quality doctor encountered ${totalErrors} blocking errors.\n`);
    process.exit(1);
}
