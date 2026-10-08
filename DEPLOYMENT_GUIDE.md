# Production deployment on cPanel

## Server layout

- cPanel Git repository: `/home/tufanconx/repo-production`
- Live Laravel application: `/home/tufanconx/public_html`
- Production branch: `production`

`public_html` is the live application directory, not the Git checkout. Do not run `git pull` from `public_html`.

## Deploy a GitHub production update

1. Push/merge the tested change to GitHub's `production` branch.
2. In cPanel, open **Git Version Control** and manage `/home/tufanconx/repo-production`.
3. Select **Update from Remote**.
4. Confirm the checked-out branch is `production` and the displayed commit is the new commit.
5. Select **Deploy HEAD Commit**.

The root `.cpanel.yml` runs `deploy/production_deploy.sh`. It syncs the application to `public_html`, preserves `.env` and runtime storage, runs Laravel migrations, and rebuilds Laravel caches. Back up the database before a production deployment that includes schema migrations.

## Make GitHub pushes deploy automatically

This cPanel repository uses pull-based deployment. A push to GitHub alone does not update the cPanel checkout. For automatic deployment, add this command once in **cPanel → Cron Jobs** to poll the `production` branch every five minutes:

```cron
*/5 * * * * /bin/bash /home/tufanconx/repo-production/deploy/run-deploy.sh >> /home/tufanconx/deploy-cron.log 2>&1
```

The poller deploys only when it detects a new production commit. The alternative is enabling the GitHub Actions SSH deployment and configuring its server secrets; do not enable it until those credentials are correctly configured.

## Troubleshooting

- `Could not open input file: artisan` means the command was run outside the Laravel root. Use `/home/tufanconx/public_html` for Artisan commands.
- `origin does not appear to be a git repository` in `public_html` is expected; the Git checkout is `/home/tufanconx/repo-production`.
- If cPanel says deployment is unavailable, first use **Update from Remote** so the checked-out branch contains `.cpanel.yml`, then verify the branch has no uncommitted changes.
- If a migration fails, do not drop production tables or mark the migration as complete manually. Keep the error output and inspect the live schema before retrying.

## Credential handling

Never commit `.env`, database exports, passwords, private keys, or cPanel session URLs. Credentials that have ever been committed must be rotated; deleting them from the latest file does not remove them from Git history.
