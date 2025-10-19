# Playwright E2E tests

This folder contains Playwright end-to-end tests for the laravel-todo app.

Quick start

1. Install deps from this folder:

```bash
cd playwright
npm install
npx playwright install
```

2. Start the Laravel app (in project root) — for example:

```bash
php -S localhost:8000 -t public
```

3. Run tests:

```bash
cd playwright
npm test
```

Environment
- PLAYWRIGHT_BASE_URL: base URL for the app (defaults to http://localhost:8000)

Notes
- Tests expect the app to be reachable at the configured base URL.
