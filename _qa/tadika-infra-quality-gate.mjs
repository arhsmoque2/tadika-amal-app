#!/usr/bin/env node
/**
 * Tadika Amal Apps — Cloud Infrastructure Quality Gate (v2.0 - Hardened)
 * Hermetically validates Cloud Run, Neon Postgres, and Cloudflare R2 configs
 * with ZERO required external credentials, network calls, or cloud CLIs.
 * Designed for full autonomous execution in ephemeral cloud sandboxes.
 */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const defaultRepoRoot = path.resolve(__dirname, '..');

/**
 * Remove comments from Dockerfile text
 */
function stripDockerComments(content) {
    return content
        .split('\n')
        .map(line => {
            const trimmed = line.trim();
            if (trimmed.startsWith('#') && !trimmed.startsWith('# syntax=')) {
                return '';
            }
            return line;
        })
        .join('\n');
}

/**
 * Core validation function exportable for unit testing
 */
export function validateInfrastructure(customRepoRoot = defaultRepoRoot) {
    const repoRoot = customRepoRoot;
    const results = {
        totalErrors: 0,
        totalWarnings: 0,
        errors: [],
        warnings: [],
        details: []
    };

    function logError(msg) {
        results.totalErrors++;
        results.errors.push(msg);
        results.details.push(`  ❌ ${msg}`);
    }

    function logWarning(msg) {
        results.totalWarnings++;
        results.warnings.push(msg);
        results.details.push(`  ⚠️ ${msg}`);
    }

    function logSuccess(msg) {
        results.details.push(`  ✅ ${msg}`);
    }

    // --- 1. Cloud Run Container & Dockerfile Invariants ---
    results.details.push('🐳 Gate 1: Cloud Run Container & Dockerfile Invariant...');
    const dockerfilePath = path.join(repoRoot, 'Dockerfile');
    if (!fs.existsSync(dockerfilePath)) {
        logError('Missing Dockerfile');
    } else {
        const rawDockerContent = fs.readFileSync(dockerfilePath, 'utf8');
        const strippedDocker = stripDockerComments(rawDockerContent);

        // Verify FROM instructions
        const fromLines = strippedDocker.match(/^\s*FROM\s+([^\s]+)/gim) || [];
        const fromImages = fromLines.map(l => l.replace(/^\s*FROM\s+/i, '').split(/\s+/)[0]);
        
        const hasComposer = fromImages.some(img => img.startsWith('composer:2'));
        const hasNode = fromImages.some(img => img.startsWith('node:22-alpine'));
        const hasFrankenPhp = fromImages.some(img => img.startsWith('dunglas/frankenphp:1-php8.4'));

        if (!hasComposer || !hasNode) {
            logError('Dockerfile missing standard multi-stage build stages (composer:2, node:22-alpine).');
        } else {
            logSuccess('Multi-stage build stages (vendor, assets) verified.');
        }

        if (!hasFrankenPhp) {
            logError('Dockerfile does not use dunglas/frankenphp:1-php8.4-bookworm runtime base image.');
        } else {
            logSuccess('FrankenPHP 8.4 production runtime base image verified.');
        }

        // Verify PHP Extensions inside install-php-extensions command
        const installExtMatch = strippedDocker.match(/install-php-extensions\s+((?:[a-z0-9_]+\s*|\\\s*\n\s*)+)/i);
        if (!installExtMatch) {
            logError('Dockerfile missing install-php-extensions command block.');
        } else {
            const rawExtBlock = installExtMatch[1].replace(/\\\r?\n/g, ' ');
            const installedExts = rawExtBlock.split(/\s+/).map(s => s.trim()).filter(Boolean);
            const requiredExtensions = ['pdo_pgsql', 'pgsql', 'pdo_sqlite', 'gd', 'zip', 'intl', 'bcmath', 'opcache', 'pcntl'];
            const missingExts = requiredExtensions.filter(ext => !installedExts.includes(ext));

            if (missingExts.length > 0) {
                logError(`Dockerfile missing essential PHP extensions in install-php-extensions: ${missingExts.join(', ')}`);
            } else {
                logSuccess(`Dockerfile contains all ${requiredExtensions.length} required PHP extensions.`);
            }
        }
    }

    // --- 2. Cloudflare R2 Object Storage Invariants ---
    results.details.push('📦 Gate 2: Cloudflare R2 Object Storage Configuration...');
    const filesystemsPath = path.join(repoRoot, 'config', 'filesystems.php');
    if (!fs.existsSync(filesystemsPath)) {
        logError('Missing config/filesystems.php');
    } else {
        const fsContent = fs.readFileSync(filesystemsPath, 'utf8');
        const hasEndpoint = fsContent.includes("'endpoint' => env('AWS_ENDPOINT')");
        const hasPathStyle = fsContent.includes("'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false)");
        const hasBucket = fsContent.includes("'bucket' => env('AWS_BUCKET')");

        if (!hasEndpoint || !hasPathStyle || !hasBucket) {
            logError('config/filesystems.php missing Cloudflare R2 S3 adapter bindings (AWS_ENDPOINT, AWS_USE_PATH_STYLE_ENDPOINT, AWS_BUCKET).');
        } else {
            logSuccess('config/filesystems.php S3/R2 Flysystem adapter properly mapped.');
        }
    }

    // --- 3. Google Cloud Run & Secret Manager Reference Safety ---
    results.details.push('🚀 Gate 3: Cloud Run Deployment & Secret Manager Safety...');
    const deployWorkflowPath = path.join(repoRoot, '.github', 'workflows', 'deploy.yml');
    if (!fs.existsSync(deployWorkflowPath)) {
        logError('Missing .github/workflows/deploy.yml');
    } else {
        const deployContent = fs.readFileSync(deployWorkflowPath, 'utf8');
        
        // Check WIF Keyless Auth
        if (deployContent.includes('workload_identity_provider') && deployContent.includes('service_account')) {
            logSuccess('Keyless Workload Identity Federation (WIF) auth configured.');
        } else {
            logError('deploy.yml missing Workload Identity Federation configuration.');
        }

        // Check Secret Manager References vs Plaintext Env Vars
        const secretVars = ['DATABASE_URL', 'APP_KEY', 'AWS_SECRET_ACCESS_KEY'];
        let leakedSecrets = 0;
        for (const sec of secretVars) {
            const regex = new RegExp(`--set-env-vars=[^\\n]*\\b${sec}=`, 'i');
            if (regex.test(deployContent)) {
                logError(`deploy.yml passes sensitive credential '${sec}' via --set-env-vars! Must use Secret Manager references.`);
                leakedSecrets++;
            }
        }
        if (leakedSecrets === 0) {
            logSuccess('Zero sensitive credentials passed via plaintext --set-env-vars in deploy.yml.');
        }

        // Verify Cloud Run Job for Migrations
        if (deployContent.includes('gcloud run jobs deploy tadika-migrate') && deployContent.includes('php artisan migrate --force')) {
            logSuccess('Standalone tadika-migrate Cloud Run Job configured for safe schema migrations.');
        } else {
            logError('deploy.yml missing standalone tadika-migrate Cloud Run Job execution.');
        }

        // Verify Scheduler Cron Job
        if (deployContent.includes('tadika-scheduler') && deployContent.includes('* * * * *')) {
            logSuccess('1-minute Cloud Scheduler trigger for tadika-scheduler verified.');
        } else {
            logWarning('Cloud Scheduler trigger not found or does not use standard 1-minute cron.');
        }
    }

    // --- 4. Neon PostgreSQL Dual Connection & Ephemeral PR Invariants ---
    results.details.push('🐘 Gate 4: Neon PostgreSQL Dual Connection & PR Branching...');
    const neonWorkflowPath = path.join(repoRoot, '.github', 'workflows', 'neon_workflow.yml');
    if (!fs.existsSync(neonWorkflowPath)) {
        logError('Missing .github/workflows/neon_workflow.yml');
    } else {
        const neonContent = fs.readFileSync(neonWorkflowPath, 'utf8');
        
        // Check ephemeral branch creation and deletion
        const hasCreate = neonContent.includes('neondatabase/create-branch-action');
        const hasDelete = neonContent.includes('neondatabase/delete-branch-action');
        
        if (hasCreate && hasDelete) {
            logSuccess('Ephemeral PR database branch lifecycle (create + teardown on close) verified.');
        } else {
            logError('neon_workflow.yml missing create or delete branch action.');
        }

        // Check database.php pgsql connection URL configuration
        const dbConfigPath = path.join(repoRoot, 'config', 'database.php');
        if (fs.existsSync(dbConfigPath)) {
            const dbContent = fs.readFileSync(dbConfigPath, 'utf8');
            if (dbContent.includes("'url' => env('DB_URL', env('DATABASE_URL'))") || dbContent.includes("'url' => env('DATABASE_URL')")) {
                logSuccess('config/database.php pgsql driver accepts standard DATABASE_URL parameter.');
            } else {
                logError('config/database.php pgsql driver missing DATABASE_URL fallback mapping.');
            }
        }

        // Strictly verify PR comment step exists and is completely sanitized
        const hasCommentAction = neonContent.includes('thollander/actions-comment-pull-request');
        if (!hasCommentAction) {
            logError('neon_workflow.yml missing mandatory thollander/actions-comment-pull-request step.');
        } else {
            const commentMatch = neonContent.match(/thollander\/actions-comment-pull-request[\s\S]*?message:\s*\|([\s\S]*?)(?:comment_tag|$)/i);
            if (!commentMatch) {
                logError('neon_workflow.yml has actions-comment-pull-request action but message body could not be parsed (formatting drift).');
            } else {
                const commentBody = commentMatch[1];
                const dangerousPatterns = [
                    { pattern: /outputs\.db_url/i, name: 'raw db_url output' },
                    { pattern: /outputs\.db_url_with_pooler/i, name: 'raw db_url_with_pooler output' },
                    { pattern: /postgres:\/\//i, name: 'postgres:// connection URI' },
                    { pattern: /postgresql:\/\//i, name: 'postgresql:// connection URI' },
                    { pattern: /password\s*[:=]/i, name: 'raw password field' }
                ];

                let leakCount = 0;
                for (const dp of dangerousPatterns) {
                    if (dp.pattern.test(commentBody)) {
                        logError(`neon_workflow.yml leaks ${dp.name} inside PR comment body!`);
                        leakCount++;
                    }
                }

                if (leakCount === 0) {
                    logSuccess('PR comment sanitized: connection strings and passwords are never posted publicly.');
                }
            }
        }
    }

    // --- 5. Zero-Ask Cloud Sandbox Independence Invariant ---
    results.details.push('🔒 Gate 5: Zero-Ask Cloud Sandbox Hermetic Test Fallback...');
    const envExamplePath = path.join(repoRoot, '.env.example');
    if (fs.existsSync(envExamplePath)) {
        const envContent = fs.readFileSync(envExamplePath, 'utf8');
        if (envContent.includes('DB_CONNECTION=') && envContent.includes('SESSION_DRIVER=')) {
            logSuccess('.env.example contains complete environment variable blueprint.');
        } else {
            logWarning('.env.example may be missing standard Laravel driver definitions.');
        }
    }

    return results;
}

// CLI Execution if run directly
if (process.argv[1] && fileURLToPath(import.meta.url) === path.resolve(process.argv[1])) {
    console.log('\n===============================================================');
    console.log('  ☁️  [TADIKA INFRA QUALITY GATE] Cloud Run, Neon & R2 Invariants');
    console.log('===============================================================\n');

    const res = validateInfrastructure(defaultRepoRoot);
    res.details.forEach(line => console.log(line));

    console.log('\n===============================================================');
    if (res.totalErrors === 0) {
        console.log(`🎉 [SUCCESS] Infrastructure Quality Gate Passed! (${res.totalWarnings} warnings)\n`);
        process.exit(0);
    } else {
        console.error(`❌ [FAILED] Infrastructure Quality Gate failed with ${res.totalErrors} errors.\n`);
        process.exit(1);
    }
}
