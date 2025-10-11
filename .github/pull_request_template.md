## Summary
Briefly describe the change, motivation, and context.

## Changes
- 
- 

## Test Plan
Describe how you tested this change locally. Include commands and results.

- `docker compose config` ✅/❌
- `make lint-php` ✅/❌
- `docker compose up -d` + `make smoke` ✅/❌
- Any additional tests/screenshots/logs

## Checklist
- [ ] No secrets in diffs; `.env` not committed
- [ ] `pre-commit run --all-files` passes (if installed)
- [ ] `docker compose config` passes
- [ ] PHP lint passes (`make lint-php`)
- [ ] Smoke tests pass locally
- [ ] README/docs updated (if behavior/ports/endpoints changed)
- [ ] CI green on this PR

## Risks & Rollout
- Potential impact or rollback steps
- Notable dependency/version changes

## Security impact
- Any changes to secrets handling, auth, or exposed ports

