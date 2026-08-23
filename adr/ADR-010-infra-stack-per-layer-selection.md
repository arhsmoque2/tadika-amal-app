# ADR-010: Per-Layer Infra Stack Selection (Cost, Sustainability, Free Tier, Performance)

**Status**: Accepted
**Date**: 2026-08-23

Supersedes the storage/database/queue/email/monitoring picks implied by this
session's earlier "Hosting Blueprint" artifact, which proposed Laravel Cloud
+ R2 without knowledge of the org's existing GCP/Cloud Run convention.

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
| Object storage | **Google Cloud Storage (GCS)** | Same-project IAM, zero setup, Always Free tier (5GB storage + 5,000 ops/day) comfortably covers admin/teacher-facing PDFs (fee receipts, JKM reports). R2's zero-egress pitch only pays off at high-volume *parent-facing* downloads — not this workload today, and it adds a cross-cloud GCP→Cloudflare hop the other direction. | Parent-facing bulk document downloads become a real, high-volume usage pattern |
| Cache / queue | **Upstash Redis** (serverless, pay-per-request) | Cloud Memorystore has **no serverless tier** — it's a provisioned instance from ~US$35/mo regardless of load, which is economically wrong paired with a scale-to-zero compute layer. Upstash has a real free tier (10k commands/day) and no VPC requirement. | Queue throughput becomes high enough and sustained enough that per-request Upstash pricing exceeds a flat Memorystore instance cost |
| Transactional email | **Amazon SES** | ~US$0.10/1,000 emails indefinitely, industry-standard deliverability, lowest platform/pricing risk over years. Resend has nicer DX and a free 3k/mo tier but is optimized for a use case (marketing + transactional mix) this app doesn't need — this app is transactional-only (fee receipts, JKM notices). | Never expected to reverse; only reconsider if SES deliverability/domain setup becomes a real blocker |
| Error monitoring | **Cloud Logging + Error Reporting** (GCP-native, included with Cloud Run) | Free, zero separate signup, no 4th vendor. Sentry's free tier (5k errors/mo) is fine but redundant given GCP already ships this. | Only add Sentry if its issue-triage/alerting UX is specifically wanted over raw GCP logs |

## Consequences

- **Two vendors outside GCP remain by design**: Neon (until the Cloud SQL
  migration trigger fires) and Upstash (indefinitely — GCP has no serverless
  Redis product that fits this traffic shape). This is accepted cost-of-fit,
  not an oversight.
- Choosing Neon now means the Cloud SQL migration is a known, planned future
  task, not a surprise — `DEVTOOLS.md`'s setup order and this ADR's trigger
  condition are the record of when to do it.
- SES over Resend trades initial setup friction (domain verification, sending
  limits during warmup) for materially lower steady-state cost — acceptable
  since this app's email volume is low-frequency/high-importance (statutory
  receipts/reports), not high-volume marketing.

## Secrets a local agent needs to prepare before deployment

None of these exist in this repo and none should ever be committed. This is
the checklist for whoever provisions the actual environment — each secret
maps directly to a decision above:

| # | Secret / credential | Where it comes from | Used by |
|---|---|---|---|
| 1 | `DATABASE_URL` (Neon connection string, pooled + direct variants) | Neon project dashboard, `ap-southeast-1` region | Laravel `.env` `DB_*` / Cloud Run env var (via Secret Manager) |
| 2 | GCS service account key (JSON) + bucket name | GCP IAM → Service Accounts, scoped to `roles/storage.objectAdmin` on one bucket only (not project-wide) | Laravel `filesystems.php` GCS disk config |
| 3 | Upstash Redis REST URL + token | Upstash console, `ap-southeast-1`-nearest region | Laravel queue/cache connection (`REDIS_URL` or Upstash's REST driver) |
| 4 | SES SMTP credentials (access key + secret) **and** verified sending domain (SPF/DKIM/DMARC DNS records) | AWS SES console; domain verification is a prerequisite, not optional — sending will be throttled/blocked until DNS propagates | Laravel `MAIL_*` env vars |
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

- **Laravel Cloud + Cloudflare R2** (this session's earlier "Hosting
  Blueprint" artifact) — reasonable in isolation, but rejected once the
  project owner confirmed Cloud Run/GCP is the correct platform based on
  existing ARH deployment practice; keeping infra on one cloud avoids the
  cross-vendor egress and ops-surface cost this ADR optimizes against.
- **Cloud SQL from day one** — rejected for now on cost: no free tier, fixed
  monthly floor regardless of the app's actual (low, spiky) traffic. Captured
  above as the planned migration target, not discarded.
- **Cloudflare R2 for all object storage** — rejected for now; revisit trigger
  given above.
- **Cloud Memorystore for Redis** — rejected; no serverless tier makes it a
  poor economic fit for a scale-to-zero compute layer.
