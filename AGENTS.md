# Repository Guidelines

## Project Structure & Module Organization

- `app/`: Laravel backend services, models, and import logic. Key import services live under
  `app/Http/Services/HoldingImport/`.
- `resources/js/`: Vue 3 front-end components and pages. Shared UI components reside in `resources/js/Components/`.
- `database/`: Migrations, seeders, and model factories. Feature tests depend on factories in `database/factories/`.
- `tests/Feature/`: Pest feature tests covering import flows and activity logging.

## Build, Test, and Development Commands

- `composer install`: Install PHP dependencies defined in `composer.json`.
- `npm install`: Install JS dependencies for the Vue frontend and Vite tooling.
- `php artisan test`: Run the global test suite (Pest/Laravel).
- `npm run lint`: Lint Vue and JavaScript assets according to project rules.

## Coding Style & Naming Conventions

- PHP: Follow Laravel defaults; PSR-12 formatting enforced via `./vendor/bin/pint --dirty`. Don't ever run it without
  the --dirty flag.
- Vue/JS: Use 2-space indentation and script setup syntax where appropriate.
- Filenames: Singular models (`Company.php`), snake_case migrations, and PascalCase Vue components (e.g.,
  `CompanyInfoHeader.vue`).

## Testing Guidelines

- Framework: Pest with Laravel test helpers.
- Place feature tests in `tests/Feature/`; name files after the service under test (e.g.,
  `IndexHoldingActivityLogTest.php`).
- Run `php artisan test` before submitting changes; ensure new logic has coverage and assertions for critical paths.

## Commit & Pull Request Guidelines

- Do not commit changes on your own.
