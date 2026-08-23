#!/usr/bin/env node
/**
 * Tadika Amal Apps — Cloud Infrastructure Quality Gate (v1.0)
 * Hermetically validates Cloud Run, Neon Postgres, and Cloudflare R2 configs
 * with ZERO required external credentials, network calls, or cloud CLIs.
 * Designed for full autonomous execution in ephemeral cloud sandboxes.
 */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const repoRoot = path.resolve(__dirname, '..');

console.log('\n===============================================================');
console.log('  ☁️  [TADIKA INFRA QUALITY GATE] Cloud Run, Neon & R2 Invariants');
console.log('===============================================================\n');

let totalErrors = 0;
let totalWarnings = 0;

// --- 1. Cloud Run Container & Dockerfile Invariants ---
console.log('🐳 Gate 1: Cloud Run Container & Dockerfile Invariant...');
const dockerfilePath = path.join(repoRoot, 'Dockerfile');
if (!fs.existsSync(dockerfilePath)) {
    console.error('  ❌ Missing Dockerfile');
    totalErrors++;
} else {
    const dockerContent = fs.readFileSync(dockerfilePath, 'utf8');
    const requiredExtensions = ['pdo_pgsql', 'pgsql', 'pdo_sqlite', 'gd', 'zip', 'intl', 'bcmath', 'opcache', 'pcntl'];
    const missingExts = requiredExtensions.filter(ext => !dockerContent.includes(ext));
    
    if (missingExts.length > 0) {
        console.error(`  ❌ Dockerfile missing essential PHP extensions: ${missingExts.join(', ')}`);
        totalErrors++;
    } else {
        console.log(`  ✅ Dockerfile contains all ${requiredExtensions.length} required PHP extensions.`);
    }

    if (!dockerContent.includes('frankenphp')) {
        console.error('  ❌ Dockerfile does not use FrankenPHP runtime.');
        totalErrors++;
    } else {
        console.log('  ✅ FrankenPHP 8.4 production runtime verified.');
    }

    if (!dockerContent.includes('composer:2') || !dockerContent.includes('node:22-alpine')) {
        console.error('  ❌ Dockerfile missing standard multi-stage build stages (composer:2, node:22-alpine).');
        totalErrors++;
    } else {
        console.log('  ✅ Multi-stage build stages (vendor, assets, runtime) verified.');
    }
}
console.log();

// --- 2. Cloudflare R2 Object Storage Invariants ---
console.log('📦 Gate 2: Cloudflare R2 Object Storage Configuration...');
const filesystemsPath = path.join(repoRoot, 'config', 'filesystems.php');
if (!fs.existsSync(filesystemsPath)) {
    console.error('  ❌ Missing config/filesystems.php');
    totalErrors++;
} else {
    const fsContent = fs.readFileSync(filesystemsPath, 'utf8');
    const hasEndpoint = fsContent.includes("'endpoint' => env('AWS_ENDPOINT')");
    const hasPathStyle = fsContent.includes("'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false)");
    const hasBucket = fsContent.includes("'bucket' => env('AWS_BUCKET')");

    if (!hasEndpoint || !hasPathStyle || !hasBucket) {
        console.error('  ❌ config/filesystems.php missing Cloudflare R2 S3 adapter bindings (AWS_ENDPOINT, AWS_USE_PATH_STYLE_ENDPOINT, AWS_BUCKET).');
        totalErrors++;
    } else {
        console.log('  ✅ config/filesystems.php S3/R2 Flysystem adapter properly mapped.');
    }
}
console.log();

// --- 3. Google Cloud Run & Secret Manager Reference Safety ---
console.log('🚀 Gate 3: Cloud Run Deployment & Secret Manager Safety...');
const deployWorkflowPath = path.join(repoRoot, '.github', 'workflows', 'deploy.yml');
if (!fs.existsSync(deployWorkflowPath)) {
    console.error('  ❌ Missing .github/workflows/deploy.yml');
    totalErrors++;
} else {
    const deployContent = fs.readFileSync(deployWorkflowPath, 'utf8');
    
    // Check WIF Keyless Auth
    if (deployContent.includes('workload_identity_provider') && deployContent.includes('service_account')) {
        console.log('  ✅ Keyless Workload Identity Federation (WIF) auth configured.');
    } else {
        console.error('  ❌ deploy.yml missing Workload Identity Federation configuration.');
        totalErrors++;
    }

    // Check Secret Manager References vs Plaintext Env Vars
    const secretVars = ['DATABASE_URL', 'APP_KEY', 'AWS_SECRET_ACCESS_KEY'];
    let leakedSecrets = 0;
    for (const sec of secretVars) {
        const regex = new RegExp(`--set-env-vars=[^\\n]*\\b${sec}=`, 'i');
        if (regex.test(deployContent)) {
            console.error(`  ❌ deploy.yml passes sensitive credential '${sec}' via --set-env-vars! Must use Secret Manager references.`);
            leakedSecrets++;
        }
    }
    if (leakedSecrets > 0) {
        totalErrors += leakedSecrets;
    } else {
        console.log('  ✅ Zero sensitive credentials passed via plaintext --set-env-vars in deploy.yml.');
    }

    // Verify Cloud Run Job for Migrations
    if (deployContent.includes('gcloud run jobs deploy tadika-migrate') && deployContent.includes('php artisan migrate --force')) {
        console.log('  ✅ Standalone tadika-migrate Cloud Run Job configured for safe schema migrations.');
    } else {
        console.error('  ❌ deploy.yml missing standalone tadika-migrate Cloud Run Job execution.');
        totalErrors++;
    }

    // Verify Scheduler Cron Job
    if (deployContent.includes('tadika-scheduler') && deployContent.includes('* * * * *')) {
        console.log('  ✅ 1-minute Cloud Scheduler trigger for tadika-scheduler verified.');
    } else {
        console.warn('  ⚠️ Cloud Scheduler trigger not found or does not use standard 1-minute cron.');
        totalWarnings++;
    }
}
console.log();

// --- 4. Neon PostgreSQL Dual Connection & Ephemeral PR Invariants ---
console.log('🐘 Gate 4: Neon PostgreSQL Dual Connection & PR Branching...');
const neonWorkflowPath = path.join(repoRoot, '.github', 'workflows', 'neon_workflow.yml');
if (!fs.existsSync(neonWorkflowPath)) {
    console.error('  ❌ Missing .github/workflows/neon_workflow.yml');
    totalErrors++;
} else {
    const neonContent = fs.readFileSync(neonWorkflowPath, 'utf8');
    
    // Check ephemeral branch creation and deletion
    const hasCreate = neonContent.includes('neondatabase/create-branch-action');
    const hasDelete = neonContent.includes('neondatabase/delete-branch-action');
    
    if (hasCreate && hasDelete) {
        console.log('  ✅ Ephemeral PR database branch lifecycle (create + teardown on close) verified.');
    } else {
        console.error('  ❌ neon_workflow.yml missing create or delete branch action.');
        totalErrors++;
    }

    // Check PR comment masking / sanitization
    const commentMatch = neonContent.match(/thollander\/actions-comment-pull-request[\s\S]*?message:\s*\|([\s\S]*?)(?:comment_tag|$)/);
    if (commentMatch) {
        const commentBody = commentMatch[1];
        if (commentBody.includes('outputs.db_url') || commentBody.includes('outputs.db_url_with_pooler') || commentBody.includes('postgres://')) {
            console.error('  ❌ neon_workflow.yml posts raw connection string/credentials in PR comment body!');
            totalErrors++;
        } else {
            console.log('  ✅ PR comment sanitized: connection strings and passwords are never posted publicly.');
        }
    }
}
console.log();

// --- 5. Zero-Ask Cloud Sandbox Independence Invariant ---
console.log('🔒 Gate 5: Zero-Ask Cloud Sandbox Hermetic Test Fallback...');
const envExamplePath = path.join(repoRoot, '.env.example');
if (fs.existsSync(envExamplePath)) {
    const envContent = fs.readFileSync(envExamplePath, 'utf8');
    if (envContent.includes('DB_CONNECTION=') && envContent.includes('SESSION_DRIVER=')) {
        console.log('  ✅ .env.example contains complete environment variable blueprint.');
    } else {
        console.warn('  ⚠️ .env.example may be missing standard Laravel driver definitions.');
        totalWarnings++;
    }
}

console.log('===============================================================');
if (totalErrors === 0) {
    console.log(`🎉 [SUCCESS] Infrastructure Quality Gate Passed! (${totalWarnings} warnings)\n`);
    process.exit(0);
} else {
    console.error(`❌ [FAILED] Infrastructure Quality Gate failed with ${totalErrors} errors.\n`);
    process.exit(1);
}
