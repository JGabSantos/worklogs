# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Application Overview

**Worklogs** is a work-hour tracking system for company employees. Users register their worked hours per day (time entries), and managers/admins can view and manage them. The UI is in Portuguese (PT-BR/PT-PT).

## Commands

```bash
# Development
composer run dev          # Starts all services (Laravel + Vite + queue)
npm run build             # Build frontend assets

# Testing
php artisan test --compact                              # Run all tests
php artisan test --compact --filter=IndexTimeEntries   # Run single test class
php artisan test --compact tests/Feature/TimeEntries/  # Run a directory

# Code quality
vendor/bin/pint --dirty --format agent  # Format changed PHP files (required after every PHP edit)

# Database
php artisan migrate --no-interaction
php artisan db:seed --no-interaction

# Inspection
php artisan route:list --except-vendor
```

## Architecture

### Permission System

A custom profile-based access control system — NOT Laravel Gates/Policies.

- **Profile** groups permissions (many-to-many via `permission_profile` pivot).
- **User** belongs to one Profile; `User::hasPermission(string $code): bool` checks if the user and their profile are both active and the permission code exists in the profile's permissions.
- **`CheckPermission` middleware** (alias: `permission:{code}`) enforces this on routes. Returns 401 if unauthenticated, 403 if inactive/no permission.
- **Livewire components** also call `hasPermission()` before rendering buttons (e.g. `canCreate()`, `canEdit()`).
- Seeded permission codes: `time-entries.create.own`, `time-entries.update.own`, `time-entries.delete.own`, `time-entries.show.own`.

### Time Entry Business Logic

All time entry mutations go through `TimeEntryService` (`app/Services/TimeEntryService.php`):

- End time must be after start time.
- No overlapping entries for the same user on the same date (checked via DB query).
- `ActivityType` and `Client` must exist and be active.
- Duration in minutes is auto-calculated from start/end.
- Deletion is a soft-delete: sets `status = 'deleted'` rather than removing the row.
- Audit columns `created_by` and `updated_by` always set to the authenticated user.

### Livewire Component Communication

The `TimeEntries/` Livewire components communicate exclusively via dispatched browser events:

| Event | Dispatched by | Consumed by |
|---|---|---|
| `time-entry-created` | Create | Index, Recents |
| `time-entry-updated` | Edit | Index, Recents |
| `time-entry-deleted` | Delete | Index, Recents |
| `open-delete-time-entry-modal` | Index blade | Delete |
| `open-show-time-entry-modal` | Index blade | Show |
| `open-edit-time-entry-modal` | Index blade | Edit |

### Index Component Filters

`TimeEntries/Index.php` uses `#[Url]` attributes so all filter state lives in the URL query string. When adding new filter fields, follow the same `#[Url]` pattern. Available filters: `search`, `dateFrom`/`dateTo` (d/m/Y format in UI, converted to m-d-Y internally), `status`, `client_id`, `activity_type_id`, `duration_min`/`duration_max`, `sort_by`, `orderBy`, `perPage`.

### Key Models & Scopes

- `TimeEntry::visible()` scope — always restricts to the authenticated user's entries. Use this on every query involving time entries.
- `TimeEntry::statusNotDraft()` scope — excludes draft entries from listings.
- Date/time casts: `date` as `date`, `start_time`/`end_time` as `datetime:H:i:s`.

### Authentication

Laravel Fortify handles all auth routes (login, register, password reset, email verification, 2FA). Custom views registered via `pages::auth.*` Blade namespace. The `FortifyServiceProvider` wires up custom action classes (`CreateNewUser`, `ResetUserPassword`).

## Testing Patterns

Tests use factories exclusively to create data. The `TimeEntryFactory` depends on seeded `ActivityType` and `Client` records existing in the DB — use `RefreshDatabase` together with seeders or factory-created related models.

Livewire components are tested with `Livewire::test(Component::class, ['prop' => $value])`. Check existing tests in `tests/Feature/TimeEntries/` for the established pattern before writing new ones.

---

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- livewire/flux (FLUXUI_FREE) - v2
- livewire/livewire (LIVEWIRE) - v4
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.
- To check environment variables, read the `.env` file directly.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== livewire/core rules ===

# Livewire

- Livewire allow to build dynamic, reactive interfaces in PHP without writing JavaScript.
- You can use Alpine.js for client-side interactions instead of JavaScript frameworks.
- Keep state server-side so the UI reflects it. Validate and authorize in actions as you would in HTTP requests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

</laravel-boost-guidelines>
