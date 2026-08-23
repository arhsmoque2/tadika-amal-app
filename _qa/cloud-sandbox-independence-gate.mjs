#!/usr/bin/env node
/**
 * Tadika Amal Apps — Cloud Sandbox Agent Independence & Quality Gate
 * Enforces zero-host lock-in, portable scripts, hermetic CI reproducibility,
 * submodule integrity, test coverage presence, and target locale completeness.
 */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const repoRoot = path.resolve(__dirname, '..');

console.log('\n===============================================================');
console.log('  🌐 [CLOUD SANDBOX INDEPENDENCE GATE] Portability & CI Invariants');
console.log('===============================================================\n');

let totalErrors = 0;
let totalWarnings = 0;

// 1. Host-Specific Hardcoded Path Invariant
console.log('🖥️ Gate 1: Host-Specific Hardcoded Path Audit...');
const HOST_PATH_PATTERNS = [
    { pattern: /[D|C]:\\_ARH-AGENT-OS/i, description: 'Hardcoded ARH-AGENT-OS Windows absolute path' },
    { pattern: /[D|C]:\\Users\\/i, description: 'Hardcoded Windows user profile path' },
    { pattern: /[D|C]:\/_ARH-AGENT-OS/i, description: 'Hardcoded forward-slash ARH OS path' },
    { pattern: /[D|C]:\/ARH-GITHUB/i, description: 'Hardcoded author GitHub checkout path' },
    { pattern: /\/home\/(?:abdul|hilmi|user)\//i, description: 'Hardcoded POSIX home directory' }
];

const SCAN_EXTENSIONS = ['.json', '.js', '.mjs', '.php', '.yml', '.yaml', '.sh', '.xml', '.neon'];
const EXCLUDE_DIRS = new Set(['.git', 'node_modules', 'vendor', 'storage']);

function scanForHostPaths(dir) {
    if (!fs.existsSync(dir)) return;
    const entries = fs.readdirSync(dir, { withFileTypes: true });
    for (const entry of entries) {
        if (EXCLUDE_DIRS.has(entry.name)) continue;
        const fullPath = path.join(dir, entry.name);
        if (entry.isDirectory()) {
            scanForHostPaths(fullPath);
        } else if (entry.isFile()) {
            const ext = path.extname(entry.name).toLowerCase();
            if (!SCAN_EXTENSIONS.includes(ext)) continue;

            const content = fs.readFileSync(fullPath, 'utf8');
            for (const item of HOST_PATH_PATTERNS) {
                if (item.pattern.test(content)) {
                    console.error(`  ❌ [${path.relative(repoRoot, fullPath)}] Contains ${item.description}`);
                    totalErrors++;
                }
            }
        }
    }
}

scanForHostPaths(repoRoot);
if (totalErrors === 0) {
    console.log('  ✅ Zero host-specific hardcoded paths found across project configs & scripts.\n');
}

// 2. Package Identity & Rebranding Invariant
console.log('🏷️ Gate 2: Package Identity & Rebranding Invariant...');
const composerPath = path.join(repoRoot, 'composer.json');
if (fs.existsSync(composerPath)) {
    try {
        const composer = JSON.parse(fs.readFileSync(composerPath, 'utf8'));
        if (composer.name === 'jeffersongoncalves/filakitv4') {
            console.error("  ❌ composer.json still named upstream starter kit 'jeffersongoncalves/filakitv4'");
            totalErrors++;
        } else {
            console.log(`  ✅ composer.json package name branded: '${composer.name}'`);
        }
    } catch (e) {
        console.error(`  ❌ Error parsing composer.json: ${e.message}`);
        totalErrors++;
    }
}

const packageJsonPath = path.join(repoRoot, 'package.json');
if (fs.existsSync(packageJsonPath)) {
    try {
        const pkg = JSON.parse(fs.readFileSync(packageJsonPath, 'utf8'));
        if (pkg.name && pkg.name.includes('filakit')) {
            console.error("  ❌ package.json still references starter kit in 'name'");
            totalErrors++;
        } else {
            console.log(`  ✅ package.json package name branded: '${pkg.name}'`);
        }
    } catch (e) {
        console.error(`  ❌ Error parsing package.json: ${e.message}`);
        totalErrors++;
    }
}
console.log();

// 3. Submodule & Local Package Integrity
console.log('📦 Gate 3: Submodule & Local Package Integrity...');
const packagesDir = path.join(repoRoot, 'packages');
if (fs.existsSync(packagesDir)) {
    const pkgEntries = fs.readdirSync(packagesDir, { withFileTypes: true });
    for (const entry of pkgEntries) {
        if (entry.isDirectory()) {
            const innerSrc = path.join(packagesDir, entry.name, 'src');
            if (fs.existsSync(innerSrc)) {
                const files = fs.readdirSync(innerSrc);
                if (files.length === 0) {
                    console.error(`  ❌ packages/${entry.name}/src is empty! Potential missing submodule code.`);
                    totalErrors++;
                } else {
                    console.log(`  ✅ packages/${entry.name} vendored with ${files.length} top-level source items.`);
                }
            }
        }
    }
}
console.log();

// 4. Target Market Locale Invariant (Bahasa Malaysia for Malaysian Preschool)
console.log('🇲🇾 Gate 4: Target Market Locale Invariant (Bahasa Malaysia)...');
const msLangPath = path.join(repoRoot, 'lang', 'ms.json');
if (!fs.existsSync(msLangPath)) {
    console.error('  ❌ Missing lang/ms.json (Bahasa Malaysia translation matrix)');
    totalErrors++;
} else {
    try {
        const msDict = JSON.parse(fs.readFileSync(msLangPath, 'utf8'));
        const requiredTerms = ['Tadika', 'Kehadiran', 'Yuran', 'Kesihatan', 'Insiden', 'Jadual', 'Penilaian'];
        let matchedTerms = 0;
        const dictStr = JSON.stringify(msDict);
        for (const term of requiredTerms) {
            if (dictStr.includes(term)) matchedTerms++;
        }
        if (matchedTerms < requiredTerms.length) {
            console.warn(`  ⚠️ lang/ms.json matched ${matchedTerms}/${requiredTerms.length} domain keywords.`);
            totalWarnings++;
        } else {
            console.log(`  ✅ lang/ms.json verified with ${Object.keys(msDict).length} translation keys.`);
        }
    } catch (e) {
        console.error(`  ❌ lang/ms.json parse error: ${e.message}`);
        totalErrors++;
    }
}
console.log();

// 5. Continuous Integration (CI) Workflow Invariant
console.log('⚙️ Gate 5: GitHub Actions CI Workflow Invariant...');
const ciWorkflowPath = path.join(repoRoot, '.github', 'workflows', 'ci.yml');
if (!fs.existsSync(ciWorkflowPath)) {
    console.error('  ❌ Missing .github/workflows/ci.yml (no automated test/quality validation in CI)');
    totalErrors++;
} else {
    const ciContent = fs.readFileSync(ciWorkflowPath, 'utf8');
    if (!ciContent.includes('pull_request') || !ciContent.includes('push')) {
        console.error('  ❌ .github/workflows/ci.yml does not trigger on pull_request and push events.');
        totalErrors++;
    } else {
        console.log('  ✅ .github/workflows/ci.yml configured for PR & Push triggers.');
    }
}
console.log();

// 6. Real Feature Test Harness Invariant
console.log('🧪 Gate 6: Feature Test Harness Invariant...');
const testDir = path.join(repoRoot, 'tests', 'Feature');
if (!fs.existsSync(testDir)) {
    console.error('  ❌ Missing tests/Feature/ directory');
    totalErrors++;
} else {
    const testFiles = fs.readdirSync(testDir).filter(f => f.endsWith('Test.php') && f !== 'ExampleTest.php');
    if (testFiles.length < 3) {
        console.error(`  ❌ Feature test coverage insufficient (found ${testFiles.length} domain test files, minimum 3 required).`);
        totalErrors++;
    } else {
        console.log(`  ✅ Verified ${testFiles.length} domain feature test files in tests/Feature/:`);
        testFiles.forEach(f => console.log(`     • ${f}`));
    }
}
console.log();

console.log('===============================================================');
if (totalErrors === 0) {
    console.log(`🎉 [SUCCESS] Cloud Sandbox Independence & Portability Gate Passed! (${totalWarnings} warnings)\n`);
    process.exit(0);
} else {
    console.error(`❌ [FAILED] Cloud Sandbox Independence Gate failed with ${totalErrors} blocking errors.\n`);
    process.exit(1);
}
