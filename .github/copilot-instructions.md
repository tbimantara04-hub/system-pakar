# Copilot / AI agent quick instructions for this repository

This project is a Laravel 10 application (PHP 8.2) with a Vite-based frontend. The notes below highlight repository-specific workflows, conventions, and gotchas so an AI coding agent can be productive immediately.

- Project roots and important files
  - Laravel app code: `app/` (controllers under `app/Http/Controllers`, policies in `app/Policies`, models under `app/Models` or `app/Http/Models` depending on changes).
  - Routes: `routes/web.php`, `routes/api.php`, `routes/auth.php` — modify these for HTTP endpoints.
  - Frontend assets: `resources/js`, `resources/css`, built artifacts placed under `public/build` (Vite) and referenced by Blade templates.
  - Build configs and scripts: `composer.json` (server/PHP deps), `package.json` (vite scripts).
  - Docker entrypoint: `entrypoint.sh` and `Dockerfile` — these perform destructive actions during container start (see caution below).

- Big picture architecture
  - Backend: Laravel 10 application exposing web and API routes; uses standard Laravel service container, middleware, and policies.
  - Frontend: Vite-powered assets (development: `vite` / `npm run dev`, production: `vite build` / `npm run build`). Blade templates in `resources/views` reference built assets in `public/build`.
  - Data & state: migrations, seeders, and Eloquent models (migrations under `database/migrations`, seeders under `database/seeders`).
  - Integrations: uses packages in `composer.json` (e.g., `yajra/laravel-datatables`, `barryvdh/laravel-dompdf`, `laravel/sanctum`) — check `config/` for provider-specific settings.

- Concrete commands (copy/paste friendly)
  - Install PHP deps: `composer install` (the Dockerfile runs composer for production with `--no-dev`)
  - Prepare local env (development):
    - `cp .env.example .env` then `php artisan key:generate`
    - `php artisan migrate --seed` (use caution; Docker entrypoint uses `migrate --force`)
  - Run dev server (local): `php artisan serve` or frontend dev with `npm run dev` (Vite).
  - Build frontend for production: `npm run build`
  - Run tests: `./vendor/bin/phpunit` or `php artisan test` (phpunit config is in `phpunit.xml` — it sets testing env values and can be tailored)
  - Docker build+run: standard docker build / run using `Dockerfile`; note `entrypoint.sh` will remove `.env` and run `php artisan migrate --force` on container start.

- Critical repository-specific warnings and patterns
  - DO NOT edit the Docker `entrypoint.sh` or run containers without understanding it — it removes `.env` and runs `php artisan migrate --force`, which will wipe/modify the DB on start.
  - The `Dockerfile` expects built frontend artifacts to exist under `public/build` (it copies the whole project). When producing production images, run `npm run build` locally or in the CI stage before building the image.
  - Tests assume testing env variables are set in `phpunit.xml` (e.g., `CACHE_DRIVER=array`, `QUEUE_CONNECTION=sync`). If you'd prefer sqlite in-memory tests, uncomment and set `DB_CONNECTION=sqlite` / `DB_DATABASE=:memory:` in `phpunit.xml` or provide a tailored `.env.testing`.

- Project conventions and code patterns to follow
  - Data tables and list logic tend to live in `app/DataTables/*` (see `IIVDataTable.php`, `InterdepenDataTable.php`) — follow that structure when adding new table views.
  - Policies are present in `app/Policies`; register/scan them in `AuthServiceProvider.php`.
  - Controllers follow Laravel conventions in `app/Http/Controllers`. Use dependency injection for services and Form Requests for validation (requests are under `app/Http/Requests`).
  - Use `storage/` and `bootstrap/cache` for runtime caches; the Dockerfile sets ownership/permissions for these directories.

- Example edits and where to place them
  - Add a new route & controller endpoint:
    - `routes/web.php` -> create route -> implement Controller method in `app/Http/Controllers`.
  - Add a new migration & model:
    - `php artisan make:model Foo -m` -> implement model in `app/Models` and migration in `database/migrations`.
  - Add a feature test:
    - `php artisan make:test FooFeatureTest --feature` -> place assertions in `tests/Feature` and run `php artisan test`.

- Quality gates & quick checks for generated changes
  - After edits that touch PHP code, run: `composer install` (if dependencies changed) and `./vendor/bin/phpunit` or `php artisan test` for CI smoke tests.
  - For frontend changes, run `npm run dev` locally to check HMR, or `npm run build` to validate production build.
  - Linting/formatting: the repo includes `laravel/pint` in dev dependencies — run Pint if required by CI: `./vendor/bin/pint`.

If anything above is unclear or you want me to expand examples (CI-specific steps, safe docker usage, or template PR messages), tell me which section to iterate on and I'll update the file.
