#!/usr/bin/env node
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const repoRoot = path.resolve(__dirname, '..');
const hooksDir = path.join(repoRoot, '.git', 'hooks');

if (!fs.existsSync(hooksDir)) {
    console.log('[git-hooks] .git/hooks directory not found (skipping hook installation).');
    process.exit(0);
}

const preCommitScript = `#!/usr/bin/env sh
# Tadika Amal — Pre-commit Hook
set -e
echo "🔍 [pre-commit] Running local quality doctors & secret scanners..."
node _qa/tadika-quality-doctor.mjs
node _qa/tadika-docs-doctor.mjs
node _qa/cloud-sandbox-independence-gate.mjs
echo "✅ [pre-commit] All pre-commit checks passed."
`;

const prePushScript = `#!/usr/bin/env sh
# Tadika Amal — Pre-push Hook
set -e
echo "🚀 [pre-push] Running full QA suite and infrastructure verification..."
pnpm run qa:all
echo "✅ [pre-push] Full quality gate passed. Safe to push!"
`;

fs.writeFileSync(path.join(hooksDir, 'pre-commit'), preCommitScript, { mode: 0o755 });
fs.writeFileSync(path.join(hooksDir, 'pre-push'), prePushScript, { mode: 0o755 });

console.log('🎉 [git-hooks] Native pre-commit and pre-push hooks installed into .git/hooks/');
