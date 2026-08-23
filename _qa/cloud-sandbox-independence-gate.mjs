#!/usr/bin/env node
/**
 * Tadika Amal Apps — Cloud Sandbox Agent Independence & Quality Gate
 * Enforces zero-host lock-in, portable scripts, hermetic CI reproducibility,
 * submodule integrity, test coverage presence, target locale completeness,
 * lockfile freshness, migration integrity, and Filament property-type
 * invariance.
 *
 * IMPORTANT: this script only performs static, file-content checks — it does
 * not run 'composer install', 'pnpm run build', 'php artisan migrate', or
 * 'php artisan test'. It cannot substitute for actually running those real
 * commands (see .github/workflows/ci.yml, which does). Every gate here was
 * added because a static check *can* catch that specific class of bug
 * without needing a full install — not because static checks are sufficient
 * on their own. See errors-fixes.md ERR-023 through ERR-028 for the actual
 * CI-caught bugs that motivated Gates 7-9.
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

// 7. Lockfile Freshness Invariant (composer.lock / pnpm-lock.yaml vs their manifests)
console.log('🔒 Gate 7: Lockfile Freshness Invariant...');
const composerJsonPath = path.join(repoRoot, 'composer.json');
const composerLockPath = path.join(repoRoot, 'composer.lock');
if (fs.existsSync(composerJsonPath) && fs.existsSync(composerLockPath)) {
    try {
        const composerJson = JSON.parse(fs.readFileSync(composerJsonPath, 'utf8'));
        const composerLock = JSON.parse(fs.readFileSync(composerLockPath, 'utf8'));
        const lockedNames = new Set([
            ...(composerLock.packages || []).map(p => p.name),
            ...(composerLock['packages-dev'] || []).map(p => p.name),
        ]);
        const isPlatformPackage = name => name === 'php' || name.startsWith('ext-') || name.startsWith('lib-') || name === 'composer-plugin-api' || name === 'composer-runtime-api';
        const required = [
            ...Object.keys(composerJson.require || {}),
            ...Object.keys(composerJson['require-dev'] || {}),
        ].filter(name => !isPlatformPackage(name));
        const missingFromLock = required.filter(name => !lockedNames.has(name));
        if (missingFromLock.length > 0) {
            console.error(`  ❌ composer.lock is stale: ${missingFromLock.join(', ')} required in composer.json but missing from composer.lock. Run 'composer update ${missingFromLock.join(' ')}'.`);
            totalErrors++;
        } else {
            console.log(`  ✅ composer.lock covers all ${required.length} composer.json dependencies.`);
        }
    } catch (e) {
        console.error(`  ❌ Error validating composer.lock freshness: ${e.message}`);
        totalErrors++;
    }
}

const pkgJsonPath = path.join(repoRoot, 'package.json');
const pnpmLockPath = path.join(repoRoot, 'pnpm-lock.yaml');
if (fs.existsSync(pkgJsonPath) && fs.existsSync(pnpmLockPath)) {
    try {
        const pkgJson = JSON.parse(fs.readFileSync(pkgJsonPath, 'utf8'));
        const declaredOverrides = (pkgJson.pnpm && pkgJson.pnpm.overrides) || {};
        const lockContent = fs.readFileSync(pnpmLockPath, 'utf8');
        // pnpm-lock.yaml records the resolved overrides in a top-level `overrides:` block,
        // e.g. "overrides:\n  rollup: '>=4.59.0'\n  yaml: '>=2.8.3'\n". Extract it without a
        // YAML dependency, matching this file's zero-extra-dependency style.
        const overridesBlockMatch = lockContent.match(/^overrides:\n((?:  \S.*\n)+)/m);
        const lockedOverridesText = overridesBlockMatch ? overridesBlockMatch[1] : '';
        const declaredKeys = Object.keys(declaredOverrides);
        const missingOverrides = declaredKeys.filter(key => !new RegExp(`^  ${key}:`, 'm').test(lockedOverridesText));
        if (declaredKeys.length > 0 && (missingOverrides.length > 0 || !overridesBlockMatch)) {
            console.error(`  ❌ pnpm-lock.yaml is stale: package.json's pnpm.overrides (${declaredKeys.join(', ')}) is not reflected in pnpm-lock.yaml's overrides block. Run 'pnpm install --no-frozen-lockfile'.`);
            totalErrors++;
        } else {
            console.log(`  ✅ pnpm-lock.yaml overrides block matches package.json's pnpm.overrides.`);
        }
    } catch (e) {
        console.error(`  ❌ Error validating pnpm-lock.yaml freshness: ${e.message}`);
        totalErrors++;
    }
}
console.log();

// 8. Migration Integrity Invariant (duplicate columns within a table, duplicate table creation)
console.log('🗄️ Gate 8: Migration Integrity Invariant...');
const migrationsDir = path.join(repoRoot, 'database', 'migrations');
if (fs.existsSync(migrationsDir)) {
    const migrationFiles = fs.readdirSync(migrationsDir).filter(f => f.endsWith('.php'));
    const tableCreators = new Map(); // table name -> [migration files that create it]
    let migrationIssues = 0;

    for (const file of migrationFiles) {
        const content = fs.readFileSync(path.join(migrationsDir, file), 'utf8');
        const createRegex = /Schema::create\(\s*'(\w+)'\s*,\s*function\s*\(Blueprint\s+\$table\)\s*\{/g;
        let match;
        while ((match = createRegex.exec(content)) !== null) {
            const table = match[1];
            const bodyStart = match.index + match[0].length;
            let depth = 1;
            let i = bodyStart;
            while (depth > 0 && i < content.length) {
                if (content[i] === '{') depth++;
                else if (content[i] === '}') depth--;
                i++;
            }
            const body = content.slice(bodyStart, i);
            const colMatches = [...body.matchAll(/\$table->\w+\(\s*'([a-z_]+)'/g)].map(m => m[1]);
            const dupCols = [...new Set(colMatches.filter(c => colMatches.filter(x => x === c).length > 1))];
            if (dupCols.length > 0) {
                console.error(`  ❌ [${file}] table '${table}' declares duplicate column(s) in the same Schema::create() block: ${dupCols.join(', ')}`);
                migrationIssues++;
            }

            if (!tableCreators.has(table)) tableCreators.set(table, []);
            tableCreators.get(table).push(file);
        }
    }

    for (const [table, files] of tableCreators) {
        if (files.length > 1) {
            console.error(`  ❌ Table '${table}' is created by ${files.length} different migrations: ${files.join(', ')}`);
            migrationIssues++;
        }
    }

    totalErrors += migrationIssues;
    if (migrationIssues === 0) {
        console.log(`  ✅ Scanned ${migrationFiles.length} migration files: no duplicate columns or duplicate table creation.`);
    }
}
console.log();

// 9. Filament Navigation Icon Type Invariance (Filament v4 requires string|BackedEnum|null)
console.log('🎨 Gate 9: Filament Property Type Invariance Invariant...');
const filamentDir = path.join(repoRoot, 'app', 'Filament');
if (fs.existsSync(filamentDir)) {
    let typeIssues = 0;
    function scanFilamentTypes(dir) {
        for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
            const fullPath = path.join(dir, entry.name);
            if (entry.isDirectory()) {
                scanFilamentTypes(fullPath);
            } else if (entry.isFile() && entry.name.endsWith('.php')) {
                const content = fs.readFileSync(fullPath, 'utf8');
                if (/protected\s+static\s+\?string\s+\$navigationIcon\b/.test(content)) {
                    console.error(`  ❌ [${path.relative(repoRoot, fullPath)}] declares '\$navigationIcon' as '?string' — Filament v4's Page/Resource base class requires 'string|BackedEnum|null'. This is a PHP property-type invariance violation and fatals at class-load time, not a lint warning.`);
                    typeIssues++;
                }
            }
        }
    }
    scanFilamentTypes(filamentDir);
    totalErrors += typeIssues;
    if (typeIssues === 0) {
        console.log('  ✅ All Filament Page/Resource classes declare $navigationIcon as string|BackedEnum|null.');
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
