import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import os from 'node:os';
import { validateInfrastructure } from '../tadika-infra-quality-gate.mjs';

function createFixtureWorkspace() {
    const tempDir = fs.mkdtempSync(path.join(os.tmpdir(), 'tadika-infra-test-'));

    // Base valid layout
    fs.writeFileSync(path.join(tempDir, 'Dockerfile'), `
FROM composer:2 AS vendor
FROM node:22-alpine AS assets
FROM dunglas/frankenphp:1-php8.4-bookworm AS runtime
RUN install-php-extensions \\
    pdo_pgsql \\
    pgsql \\
    pdo_sqlite \\
    gd \\
    zip \\
    intl \\
    bcmath \\
    opcache \\
    pcntl
`);

    fs.mkdirSync(path.join(tempDir, 'config'), { recursive: true });
    fs.writeFileSync(path.join(tempDir, 'config', 'filesystems.php'), `
<?php
return [
    'disks' => [
        's3' => [
            'driver' => 's3',
            'endpoint' => env('AWS_ENDPOINT'),
            'bucket' => env('AWS_BUCKET'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        ],
    ],
];
`);

    fs.mkdirSync(path.join(tempDir, '.github', 'workflows'), { recursive: true });
    fs.writeFileSync(path.join(tempDir, '.github', 'workflows', 'deploy.yml'), `
name: Deploy
jobs:
  deploy:
    steps:
      - uses: google-github-actions/auth@v3
        with:
          workload_identity_provider: projects/123/locations/global/workloadIdentityPools/pool/providers/prov
          service_account: deployer@project.iam.gserviceaccount.com
      - run: gcloud run jobs deploy tadika-migrate --args="sh,-c,php artisan migrate --force"
      - run: gcloud run jobs deploy tadika-scheduler --schedule="* * * * *"
`);

    fs.writeFileSync(path.join(tempDir, '.github', 'workflows', 'neon_workflow.yml'), `
name: Neon Branching
jobs:
  create:
    steps:
      - uses: neondatabase/create-branch-action@v5
      - uses: neondatabase/delete-branch-action@v3
      - uses: thollander/actions-comment-pull-request@v2
        with:
          message: |
            ## Neon Preview Branch Created
            Branch created for test.
          comment_tag: neon-branch-preview
`);

    fs.writeFileSync(path.join(tempDir, '.env.example'), `
DB_CONNECTION=sqlite
SESSION_DRIVER=database
`);

    return tempDir;
}

test('validateInfrastructure passes on standard valid layout', () => {
    const fixtureDir = createFixtureWorkspace();
    try {
        const res = validateInfrastructure(fixtureDir);
        assert.equal(res.totalErrors, 0, `Expected 0 errors, got: ${res.errors.join('; ')}`);
    } finally {
        fs.rmSync(fixtureDir, { recursive: true, force: true });
    }
});

test('Gate 1: Fails when PHP extensions are only present in comments', () => {
    const fixtureDir = createFixtureWorkspace();
    try {
        // Corrupt Dockerfile by commenting out install-php-extensions
        fs.writeFileSync(path.join(fixtureDir, 'Dockerfile'), `
FROM composer:2 AS vendor
FROM node:22-alpine AS assets
FROM dunglas/frankenphp:1-php8.4-bookworm AS runtime
# RUN install-php-extensions pdo_pgsql pgsql pdo_sqlite gd zip intl bcmath opcache pcntl
RUN echo "no extensions installed"
`);
        const res = validateInfrastructure(fixtureDir);
        assert.ok(res.totalErrors > 0, 'Gate 1 must fail when extensions are commented out');
        assert.ok(res.errors.some(e => e.includes('missing install-php-extensions command block')));
    } finally {
        fs.rmSync(fixtureDir, { recursive: true, force: true });
    }
});

test('Gate 2: Fails when R2/S3 storage config is missing AWS_ENDPOINT', () => {
    const fixtureDir = createFixtureWorkspace();
    try {
        fs.writeFileSync(path.join(fixtureDir, 'config', 'filesystems.php'), `
<?php
return [
    'disks' => [
        's3' => [
            'driver' => 's3',
            'bucket' => env('AWS_BUCKET'),
        ],
    ],
];
`);
        const res = validateInfrastructure(fixtureDir);
        assert.ok(res.totalErrors > 0, 'Gate 2 must fail when AWS_ENDPOINT is missing');
        assert.ok(res.errors.some(e => e.includes('missing Cloudflare R2 S3 adapter bindings')));
    } finally {
        fs.rmSync(fixtureDir, { recursive: true, force: true });
    }
});

test('Gate 3: Fails when deploy.yml leaks sensitive credentials in --set-env-vars', () => {
    const fixtureDir = createFixtureWorkspace();
    try {
        fs.writeFileSync(path.join(fixtureDir, '.github', 'workflows', 'deploy.yml'), `
name: Deploy
jobs:
  deploy:
    steps:
      - uses: google-github-actions/auth@v3
        with:
          workload_identity_provider: projects/123/locations/global/workloadIdentityPools/pool/providers/prov
          service_account: deployer@project.iam.gserviceaccount.com
      - run: gcloud run deploy tadika --set-env-vars="APP_ENV=prod,DATABASE_URL=postgres://user:pass@host/db"
      - run: gcloud run jobs deploy tadika-migrate --args="sh,-c,php artisan migrate --force"
      - run: gcloud run jobs deploy tadika-scheduler --schedule="* * * * *"
`);
        const res = validateInfrastructure(fixtureDir);
        assert.ok(res.totalErrors > 0, 'Gate 3 must fail when DATABASE_URL is in --set-env-vars');
        assert.ok(res.errors.some(e => e.includes('passes sensitive credential \'DATABASE_URL\' via --set-env-vars')));
    } finally {
        fs.rmSync(fixtureDir, { recursive: true, force: true });
    }
});

test('Gate 4: Fails when neon_workflow.yml embeds raw db_url or postgres:// in PR comment', () => {
    const fixtureDir = createFixtureWorkspace();
    try {
        fs.writeFileSync(path.join(fixtureDir, '.github', 'workflows', 'neon_workflow.yml'), `
name: Neon Branching
jobs:
  create:
    steps:
      - uses: neondatabase/create-branch-action@v5
      - uses: neondatabase/delete-branch-action@v3
      - uses: thollander/actions-comment-pull-request@v2
        with:
          message: |
            ## Neon Preview Branch Created
            Database URL: \${{ steps.create_branch.outputs.db_url }}
          comment_tag: neon-branch-preview
`);
        const res = validateInfrastructure(fixtureDir);
        assert.ok(res.totalErrors > 0, 'Gate 4 must fail when raw outputs.db_url is in PR comment');
        assert.ok(res.errors.some(e => e.includes('leaks raw db_url output inside PR comment body')));
    } finally {
        fs.rmSync(fixtureDir, { recursive: true, force: true });
    }
});

test('Gate 4: Fails when neon_workflow.yml is missing PR comment action entirely', () => {
    const fixtureDir = createFixtureWorkspace();
    try {
        fs.writeFileSync(path.join(fixtureDir, '.github', 'workflows', 'neon_workflow.yml'), `
name: Neon Branching
jobs:
  create:
    steps:
      - uses: neondatabase/create-branch-action@v5
      - uses: neondatabase/delete-branch-action@v3
`);
        const res = validateInfrastructure(fixtureDir);
        assert.ok(res.totalErrors > 0, 'Gate 4 must fail when comment action is completely missing');
        assert.ok(res.errors.some(e => e.includes('missing mandatory thollander/actions-comment-pull-request step')));
    } finally {
        fs.rmSync(fixtureDir, { recursive: true, force: true });
    }
});
