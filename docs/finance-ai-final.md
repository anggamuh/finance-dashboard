# Finance AI — Final Workspace

## Modules

- Gemini chat with bounded context and existing free-tier configuration.
- Six-month Accurate charts and financial insights from server-computed aggregates.
- Transaction Investigator: optionally filter by expense account and minimum nominal; returns at most 50 detailed rows, total count and amount across all matched rows. No AI-authored SQL.
- Anomaly flags: same date/account/amount potential duplicate; >=3x account average for accounts with at least five transactions and value >=Rp1 million. Signals are **not** proof of errors.
- Reconciliation summary for batches owned by signed-in user, with matched/suggested/unmatched separated.
- Save month snapshot: deterministic calculation, encrypted JSON at rest, 36 saved reports per user, printer-friendly standalone report accessible to owner only, browser Print / Save as PDF.
- Optional encrypted chat history: save/load/delete own conversations, maximum 25 chat sessions, with no automatic saving.

## Deploy on Hostinger

1. Review and merge PR. Ensure clean working tree: `git status`.
2. SSH: `git pull --ff-only origin main`.
3. **New migration needed**: `php artisan migrate --force`.
4. Clear Blade cache: `php artisan view:clear`. If routes are cached: `php artisan route:clear`; run `php artisan config:clear` if needed.
5. No npm install, Vite rebuild, Tailwind rebuild, Composer dependency, or new env variables needed. Requires the existing `APP_KEY` to stay stable for encrypted saved chat/report fields.
6. Open Finance AI and verify all four tabs, owner access on printable report, saved chat restoration, current month selection, and existing Gemini Q&A.

## Privacy and limits

- Chat is sent to Gemini on user prompt, with only aggregates (no raw transaction descriptions). In-page investigator detail remains on server.
- Saved report JSON may contain account labels and reconciliation names; it is encrypted with the application's existing APP_KEY. Never rotate APP_KEY without a migration/key-rotation strategy.
- The user can opt to store chat; it may contain sensitive text the user typed. Chats/reports are user-scoped and deletable.
- This is a financial assistant, not a substitute for audit. Always review proposed anomalies and confirm reconciliation manually.
- Existing application-level financial data visibility is inherited. Organizations with separate role-based access still need role permissions.

## Tests

Run only in local or disposable test database: `php artisan test --filter=FinanceAiFinalTest`.
Tests were authored via GitHub connector but could not be executed in production environment.
