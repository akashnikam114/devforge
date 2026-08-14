# Laravel Template

This folder contains Laravel-specific starter template support.

Laravel is generated through Composer as a Laravel 10 project, then DevForge applies setup steps from `manifest.json`.

## Folder structure

```text
languages/laravel/
  manifest.json              Composer/Laravel install configuration
  README.md                  Laravel template notes
  templates/                 Base files copied after Laravel is created
  features/
    manifest.json            Feature registry, prompts, env keys, and file mapping
    <feature-id>/            Feature files copied into generated Laravel projects
```

Keep Laravel PHP, Blade, config, migration, route, and service starter files inside `features/<feature-id>/`. The CLI should only read the feature manifest and copy/apply those files.

The generator checks PHP and Composer first, asks for database settings, validates the connection, asks for confirmation before running Composer commands, installs required packages, publishes config files, creates the storage link, and clears Laravel caches.

Root hosting files such as `.htaccess` and `index.php` live in `templates/` and are applied after the Laravel project is created.

Configured packages:

- laravel/ui
- maatwebsite/excel
- google/auth
- tymon/jwt-auth
- yajra/laravel-datatables-oracle
- barryvdh/laravel-dompdf
- guzzlehttp/guzzle

Excel setup creates:

- `app/Exports/SampleExport.php`

Laravel create prompts are grouped by feature so database, admin, API, middleware, Firebase, reporting, PWA, and UI setup are not asked repeatedly.

RBAC is always included. It creates:

- `database/migrations/2024_01_01_000001_create_roles_table.php`
- `database/migrations/2024_01_01_000002_add_role_id_to_users_table.php`
- `app/Models/Role.php`
- `role_id` foreign key support on `app/Models/User.php` when the Laravel model exists

Project Structure feature creates:

- `app/Helpers/helpers.php`
- `app/Services/.gitkeep`
- `app/Traits/.gitkeep`
- `public/assets/web/css/.gitkeep`
- `public/assets/web/images/.gitkeep`
- `public/assets/web/js/.gitkeep`
- `public/assets/web/fonts/.gitkeep`
- `storage/app/public/Others/`
- `storage/app/public/Restriction/Maintenance_Mode.png`

Queue setup is database-backed by default:

- `.env` and `.env.example` use `QUEUE_CONNECTION=database`
- `database/migrations/2024_01_01_000004_create_jobs_table.php`

API request logging adds the `api_logs` daily log channel in `config/logging.php`, writing to `storage/logs/api.log` with seven-day retention.

Feature command:

```bash
devforge feature --list
devforge feature PROJECT_NAME
devforge feature PROJECT_NAME --feature FEATURE_ID
```

Current feature ids:

- `database`
- `rbac`
- `project-structure`
- `api`
- `admin-panel`
- `admin-auth`
- `admin-ui-theme`
- `admin-assets`
- `common-core`
- `app-release`
- `admin-activity-log`
- `users`
- `api-rate-limit`
- `payment-gateway`
- `middleware`
- `firebase`
- `excel-export`
- `pdf-export`
- `pwa`

`common-core` is the completed reusable admin starter pack. It contains:

- admin auth and dashboard
- DashLite demo1 CSS, JavaScript, and fonts under `public/assets/admin`
- generic admin images under `public/assets/admin/images`: sidebar-ready `app-logo.png`, `default-image.png`, and `favicons/favicon.ico`
- DashLite-native sidebar spacing/hover behavior and DataTables' native processing loader
- Business Settings app logo upload with preview
- PWA icon under `public/pwa/app-icon.png`
- BusinessSetting, GeneralSetting, AppRelease, restriction settings, banners, and push notification modules
- helpers: `BusinessSettingHelper`, `GeneralHelper`, `EncryptionHelper`
- Firebase service module
- admin/common models, services, controllers, migrations, views, routes, and middleware
- `config/app-ui.php` for dynamic app name, primary color, secondary color, panel title, and panel description
- `APP_UI_*` environment keys for generated application UI settings
- `JWT_TTL=30` and `JWT_REFRESH_TTL=43200`
- root `.htaccess`, root `index.php`, root `server.php`, and `public/.htaccess`

Admin UI setup applies the selected theme color to buttons, header/profile bar states, dropdown actions, form focus states, sidebar menu states, pagination, badges, dashboard card accents, and Select2 focus/highlight states. General Settings uses Select2 for language, date format, and time format. Business Settings uses Select2 for currency and OTP provider, while sensitive values such as `encryption_key` remain stored but are not shown in the admin panel.

`RequestResponseAdapter` converts incoming request keys from `camelCase` to `snake_case` before controller processing and converts JSON response keys from `snake_case` back to `camelCase`.

`admin-activity-log` adds Spatie Activitylog config, an `activity_log` migration, login/logout listeners, admin request logging middleware, routes, sidebar link, and Activity Logs UI.

`api-rate-limit` adds `ApiRateLimitGuard` with `API_RATE_LIMIT_PER_MINUTE` and `API_RATE_LIMIT_BLOCK_DURATION_SECONDS` environment settings.

`users` adds an admin users listing and details page for DevForge's default users table.

`payment-gateway` adds selected provider scaffolding for Razorpay, Easebuzz, or PhonePe. Razorpay supports PG only; Easebuzz and PhonePe support PG, autopay, or both.

Middleware setup registers secure headers globally, keeps API request normalization/logging/sanitization in the API middleware group, and registers `api.auth`, `app.maintenance`, and `admin.maintenance` aliases. `AppServiceProvider` shares the common `appSetting` helper with all Blade views.

When `helpers.php` is selected in a full Laravel install, DevForge registers it in `composer.json > autoload.files` and refreshes Composer autoload files.

Base setup also updates:

- `config/app.php` timezone
- `config/app.php` package providers and aliases
- `config/auth.php` API JWT guard
- `app/Exceptions/Handler.php`
- `app/Providers/RouteServiceProvider.php`

Base folder structure includes:

- `app/Constants/`
- `app/Jobs/`
- `app/Listeners/`
- `app/Mails/`
- `app/Notifications/`
- `app/Http/Controllers/Admin/Auth/`
- `app/Http/Controllers/Api/V1/`
- `resources/views/admin/auth/`
- `resources/views/admin/layouts/`
- `resources/views/errors/`
- `resources/views/pages/`
- `routes/api/v1/api.php`

Route files should declare all package imports and controller imports at the top with `use` statements. Route definitions should use imported class names such as `LoginController::class` instead of inline fully qualified controller names.

Future Laravel options can be added under `recipes/`, for example:

- `api`
- `blade`
- `inertia`
- `docker`

Use `hooks/` for Laravel-specific generation steps such as installing Composer packages, copying `.env.example`, or running setup commands.
