<!-- Copilot instructions tailored for this Laravel + Vue (Vite) project -->
# Copilot agent instructions

Purpose: help AI coding agents be immediately productive in this repository (Laravel backend + Vue 3 frontend built with Vite).

- Big picture: This is a Laravel app (PHP) that serves a Vue 3 single-page/frontend via Vite. Backend PHP code lives under `app/` and `routes/`. Frontend sources are under `resources/js` and `resources/css` and are built with `vite` into `public/build`.

- Key components & where to look:
  - **Backend (Laravel)**: `app/Http/Controllers/`, `app/Models/`, `routes/web.php`, `artisan`, `config/*.php`, `database/migrations/`.
  - **Frontend (Vue + Vite)**: `resources/js/` (entry: `resources/js/app.js`, route pages under `resources/js/page/`). Example chart component: `resources/js/page/components/charts/index.vue` uses `chart.js` + `vue-chartjs`.
  - **Build and assets**: `package.json` (scripts: `dev`, `build`, `watch` using `vite`), `vite.config.js`, output in `public/build/` and `public/manifest.json`.
  - **Dev tooling / orchestration**: `Makefile`, `docker-compose*.yml` — common targets used locally include `make on-app` and container commands. When in doubt, inspect `Makefile` for project-specific targets.

- Typical workflows & commands (copyable):
  - Install PHP deps: `composer install` (use the project `composer.json`).
  - Install JS deps: `npm ci` or `npm install` then `npm run dev` for hot dev server.
  - Frontend build: `npm run build` (runs `vite build` per `package.json`).
  - Run PHP tests: vendor phpunit is available at `vendor/bin/phpunit` (see `phpunit.xml`).
  - Common Make targets: `make on-app` (dev), `make ssh-app-as-root` (may require Docker/host setup). Check `Makefile` before calling.

- Project-specific conventions and patterns (observable):
  - Laravel conventions hold: controllers in `app/Http/Controllers`, models in `app/Models`, blade views in `resources/views` for server-rendered pages.
  - Frontend is Vue 3 with Vite: code uses single-file components in `resources/js/page/` and routing via `vue-router`. Use `@vite`/`laravel-vite-plugin` integration in views.
  - Charts use `chart.js` + `vue-chartjs` — modify `resources/js/page/components/charts/index.vue` for chart UI changes.
  - Keep backend changes in PHP (follow PSR-12-ish style in existing files) and frontend changes in `resources/js` (ES modules, `type: module` in `package.json`).

- Integration points & external dependencies to be careful with:
  - `composer.json` / `vendor/` — PHP dependencies managed by Composer.
  - `package.json` / `node_modules/` — frontend deps (Vite, Vue, Chart.js). `@vitejs/plugin-vue` is present.
  - Docker/Makefile — some CI or local workflows may rely on docker-compose; confirm host permissions before running `make ssh-app-as-root`.
  - Public assets: `public/manifest.json` and `public/build/` reflect Vite outputs; ensure builds update them.

- How to modify features safely (practical examples):
  - To add a new API endpoint: add route in `routes/web.php` (or `routes/api.php` if present), implement controller under `app/Http/Controllers/`, add tests under `tests/Feature`.
  - To change the chart: edit `resources/js/page/components/charts/index.vue` and run `npm run dev` or `npm run build`; check `resources/js/app.js` to confirm component registration.

- When generating code or refactors, prefer small, focused changes and include tests:
  - Backend tests: add PHPUnit tests in `tests/Unit` or `tests/Feature` and run `vendor/bin/phpunit`.
  - Frontend checks: the repo doesn't include a JS test framework by default; run `npm run build` and manually verify browser output or add tests if requested.

- Notes for Copilot/AI agents:
  - Avoid changing global configuration without mentioning it (e.g., `vite.config.js`, `composer.json`, `package.json`, and `Makefile`) — call out the change and why it is needed.
  - Use concrete file references in patches (example: "Edit `resources/js/page/components/charts/index.vue` to update chart labels").
  - Prefer minimal patches that pass local linting and do not alter project-wide formatting rules.

If anything is unclear or you want the instructions to include more examples (e.g., a sample controller + route patch or a sample Vue component change), tell me which area to expand and I'll iterate.
