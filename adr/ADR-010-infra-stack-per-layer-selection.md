# ADR-010: Per-Layer Infra Stack Selection (Cost, Sustainability, Free Tier, Performance)

**Status**: Accepted
**Date**: 2026-08-23

Supersedes the storage/database/queue/email/monitoring picks implied by this
session's earlier "Hosting Blueprint" artifact, which proposed Laravel Cloud
+ R2 without knowledge of the org's existing GCP/Cloud Run convention.

**Revision note (2026-08-23)**: the object storage and email picks below were
corrected after review — GCS and SES were the wrong calls even on their own
stated criteria. See the "Why" column and Consequences for the reasoning;
Cloudflare R2 and Resend are the accepted picks.

## Context

ADR-009 (Cloud Run + Neon + private object storage) settled the compute platform:
**Google Cloud Run**, confirmed correct by the project owner because it matches
ARH's existing verified deployment pattern — not evaluated here again.

What ADR-009 left open, and what this ADR resolves, is the choice of pieces
*around* Cloud Run — database, object storage, cache/queue, transactional email,
and error monitoring — scored explicitly against four criteria the owner asked
for, in this order of the question asked: **long-term cost, sustainability,
free-tier generosity, performance** — not vendor familiarity.

Tadika Amal's real traffic shape matters here: a single-digit number of schools,
low concurrent admin/teacher usage, spiky (school-hours) rather than constant,
occasional PDF/DOCX/PPTX generation bursts. That shape is why several of the
picks below favor serverless/pay-per-use options over always-on GCP-native
services with a fixed monthly floor — and why each pick below also names the
**trigger condition** for when that changes.

## Decision — per layer, with the trigger for revisiting

| Layer | Pick now | Why (vs. the alternative) | Revisit when |
|---|---|---|---|
| Compute | **Cloud Run** (ADR-009, unchanged) | scale-to-zero fits spiky low-traffic usage; matches existing ARH pattern | — |
| Database | **Neon Postgres** (`ap-southeast-1`), planned migration to **Cloud SQL** later | Neon: real free tier (0.5GB, autoscaling, branching, scale-to-zero) vs. Cloud SQL's ~US$8-15/mo *floor* with no free tier. Cloud SQL wins long-term on same-VPC latency (no cross-cloud hop from Cloud Run) and single-vendor billing/ops, but only once traffic is steady enough to amortize that floor. | ≥2 schools onboarded on steady daily traffic, **or** cold-start latency after Neon idle becomes visible in logs |
| Object storage | **Cloudflare R2** | R2's free tier (10GB storage, 1M Class A + 10M Class B ops/mo, egress *always* free — not a capped allowance) is more generous than GCS's Always Free tier (5GB, ~150k ops/mo) on every axis. Worse for GCS: **its Always Free tier is restricted to specific US regions and does not apply in `asia-southeast1` (Singapore)** — the region that actually matters for Malaysia latency — so deploying GCS there means paying from byte one, while R2's free tier is region-agnostic. GCS's only real advantage (same-project IAM, no cross-cloud hop) is a performance/ops-simplicity point, not a cost or free-tier one, and doesn't outweigh a free tier that doesn't apply where we'd deploy. | GCS only becomes competitive if Google offers a Singapore-region free tier, or if same-VPC latency to storage is measured as a real problem |
| Cache / queue | **Upstash Redis** (serverless, pay-per-request) | Cloud Memorystore has **no serverless tier** — it's a provisioned instance from ~US$35/mo regardless of load, which is economically wrong paired with a scale-to-zero compute layer. Upstash has a real free tier (10k commands/day) and no VPC requirement. | Queue throughput becomes high enough and sustained enough that per-request Upstash pricing exceeds a flat Memorystore instance cost |
| Transactional email | **Resend** | Resend's free tier (3,000 emails/mo, 100/day, no expiry) very likely covers Tadika Amal's real volume forever — a single-digit number of schools sending fee receipts and JKM notices is nowhere near 100 emails/day. At that volume Resend is $0 indefinitely, which beats SES's per-email cost (SES has no free tier for accounts not sending from EC2 — billing starts at message one). SES only wins once volume exceeds Resend's free tier, which this app is not expected to do. | Sustained volume exceeds 3,000 emails/month (e.g. many more schools onboarded, or a parent-facing broadcast feature ships) |
| Error monitoring | **Cloud Logging + Error Reporting** (GCP-native, included with Cloud Run) | Free, zero separate signup, no 4th vendor. Sentry's free tier (5k errors/mo) is fine but redundant given GCP already ships this. | Only add Sentry if its issue-triage/alerting UX is specifically wanted over raw GCP logs |

## Consequences

- **Two vendors outside GCP remain by design**: Neon (until the Cloud SQL
  migration trigger fires) and Upstash (indefinitely — GCP has no serverless
  Redis product that fits this traffic shape). This is accepted cost-of-fit,
  not an oversight.
- Choosing Neon now means the Cloud SQL migration is a known, planned future
  task, not a surprise — `DEVTOOLS.md`'s setup order and this ADR's trigger
  condition are the record of when to do it.
- **One vendor outside GCP/Neon/Upstash is added**: Cloudflare, for R2. This
  is accepted the same way Neon and Upstash are — the free tier and cost
  structure fit better than the GCP-native option, and R2's S3-compatible API
  means no Laravel-side lock-in (the `flysystem-aws-s3-v3` driver Laravel
  already ships works against R2 unchanged).
- Resend over SES is a straightforward free-tier-covers-real-volume call, not
  a DX preference — reverse this only if actual sending volume data shows the
  free tier is being exceeded, not preemptively.

## Secrets a local agent needs to prepare before deployment

None of these exist in this repo and none should ever be committed. This is
the checklist for whoever provisions the actual environment — each secret
maps directly to a decision above:

| # | Secret / credential | Where it comes from | Used by |
|---|---|---|---|
| 1 | `DATABASE_URL` (Neon connection string, pooled + direct variants) | Neon project dashboard, `ap-southeast-1` region | Laravel `.env` `DB_*` / Cloud Run env var (via Secret Manager) |
| 2 | R2 API token (Access Key ID + Secret Access Key, S3-compatible) + bucket name + account-specific endpoint URL | Cloudflare dashboard → R2 → Manage API Tokens, scoped to one bucket only (not account-wide) | Laravel `filesystems.php` S3-compatible disk config (`flysystem-aws-s3-v3`, pointed at the R2 endpoint) |
| 3 | Upstash Redis REST URL + token | Upstash console, `ap-southeast-1`-nearest region | Laravel queue/cache connection (`REDIS_URL` or Upstash's REST driver) |
| 4 | Resend API key **and** verified sending domain (SPF/DKIM DNS records) | Resend dashboard; domain verification is a prerequisite, not optional — sending will be restricted to Resend's own test address until DNS propagates | Laravel `MAIL_*` env vars (`resend` mail driver) |
| 5 | Cloud Run service account + Cloud Build/Artifact Registry permissions | GCP IAM, scoped to the one Cloud Run service (least privilege — not `Editor`/`Owner`) | CI/CD deploy step |
| 6 | `APP_KEY` (Laravel) | Generated locally via `php artisan key:generate --show`, never reused across environments | Laravel `.env` |
| 7 | Secret Manager wiring (GCP Secret Manager, referenced by Cloud Run, not baked into the container image or committed `.env`) | GCP Secret Manager | All of the above, at deploy time |

**Sequencing note for the local agent**: provision #6 first (needed for any
`.env` to boot at all), then #1 and #2 (DB + storage are needed for the app to
serve any real request), then #3 (queue — needed once background jobs like
PDF/DOCX generation or notifications are exercised), then #4 (email — only
blocks features that actually send mail), then #5 last (deploy credentials,
needed only once there's something ready to deploy). Store none of these in
this repo, `.env` committed anywhere, or CI logs — Secret Manager references
only, per `_qa/cloud-sandbox-independence-gate.mjs` Gate 1's intent (host-path
and inline-secret scanning) even though that gate doesn't scan `.md`/`.env`
today — see `HANDOFF.md` §A for that gap.

## Alternatives considered and rejected

- **Laravel Cloud** (compute, from this session's earlier "Hosting Blueprint"
  artifact) — reasonable in isolation, but rejected once the project owner
  confirmed Cloud Run/GCP is the correct platform based on existing ARH
  deployment practice.
- **Cloud SQL from day one** — rejected for now on cost: no free tier, fixed
  monthly floor regardless of the app's actual (low, spiky) traffic. Captured
  above as the planned migration target, not discarded.
- **Google Cloud Storage for object storage** — rejected: smaller free tier
  than R2, egress not free even within the free tier, and the Always Free
  tier doesn't apply in the Singapore region this app would actually deploy
  to. Revisit only per the trigger given above.
- **Cloud Memorystore for Redis** — rejected; no serverless tier makes it a
  poor economic fit for a scale-to-zero compute layer.
- **Amazon SES for email** — rejected for now: no free tier for this app's
  account type, and Resend's free tier is expected to cover 100% of this
  app's real volume indefinitely. Revisit only per the trigger given above.
