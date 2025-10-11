# Secrets Management Guidance

This repo is designed for local/staging use with `.env` files (ignored by git). For CI and cloud, use a managed secret store.

- Local dev: copy `.env.example` to `.env`. Never commit `.env`.
- GitHub Actions: store secrets in repository settings → Secrets and variables → Actions.
  - Example variables: `DB_PASSWORD`, `MYSQL_ROOT_PASSWORD`, `TELEGRAM_BOT_TOKEN`, etc.
  - Access them in workflows via `${{ secrets.DB_PASSWORD }}` or export to containers via `env:` in compose overrides during CI.
- Cloud: prefer platform secret managers (AWS Secrets Manager, GCP Secret Manager, Azure Key Vault) or Vault.

## Example: Inject DB password in CI

If you don’t want to use default demo credentials in CI builds, add this to the smoke test job:

```yaml
    - name: Export CI secrets to env
      run: |
        echo "DB_PASSWORD=${{ secrets.DB_PASSWORD }}" >> $GITHUB_ENV
        echo "MYSQL_ROOT_PASSWORD=${{ secrets.MYSQL_ROOT_PASSWORD }}" >> $GITHUB_ENV
```

Compose will pick these up through the environment section in `php` and `mysql` services.

## Rotating secrets
- Rotate credentials regularly.
- Revoke and replace any secret suspected to be leaked.

## Do not
- Do not hard-code real credentials in repo files or images.
- Do not print secrets in logs. Use redaction where supported.
