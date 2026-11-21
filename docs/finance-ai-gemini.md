# Finance AI V1 — Gemini on Hostinger

## Deploy

1. Review and merge the pull request, then run in Hostinger SSH:
   - `git status`
   - `git pull --ff-only origin main`
   - `php artisan config:clear`
   - `php artisan route:clear`
   - `php artisan view:clear`
2. In the server-only `.env`, set `GEMINI_API_KEY=your_real_key` and `GEMINI_MODEL=gemini-2.5-flash`. Do not commit `.env`.
3. Log into Finance Dashboard, open **Finance AI** in the sidebar or `/finance-ai`, select a month and ask for operating expenses.
4. If 403, 429 or model errors occur, review your Google AI Studio API key restrictions, available model, quota and outbound HTTPS connectivity.

## Data, security and usage

- Server-only key is passed via `x-goog-api-key` in an HTTPS request.
- The AI receives only calculated financial aggregates and account group labels, not transaction details, bank account numbers or transfer references.
- Reconciliation data is scoped to the authenticated user's own batches. Other financial tables follow existing application access controls.
- 15 requests/hour/user in Laravel rate limiter. This does **not** guarantee no charges: use Google AI Studio's free tier without enabling paid billing, and review the quotas.
- AI cannot execute SQL or modify transactions. Its answers may contain mistakes; verify against source reports.
- Chats are kept in Livewire page state, not a separate chat history table.

## Test (locally/CI, not in production)

`php artisan test --filter=FinanceAiTest`

## Troubleshooting

If `php artisan config:clear` fails due to hosting restrictions, capture the error instead of deleting cache or vendor files.
