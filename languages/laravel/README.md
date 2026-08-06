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
- `role_id` support on `app/Models/User.php` when the Laravel model exists

Project Structure feature creates:

- `app/Helpers/helpers.php`
- `app/Services/.gitkeep`
- `app/Traits/.gitkeep`
- `public/assets/css/.gitkeep`
- `public/assets/images/.gitkeep`
- `public/assets/js/.gitkeep`
- `public/assets/fonts/.gitkeep`

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
- `middleware`
- `firebase`
- `excel-export`
- `pdf-export`
- `pwa`

`common-core` is the completed reusable admin starter pack. It contains:

- admin auth and dashboard
- DashLite-compatible admin assets under `public/assets/admin`
- BusinessSetting, GeneralSetting, restriction settings, banners, and push notification modules
- helpers: `BusinessSettingHelper`, `GeneralHelper`, `EncryptionHelper`
- Firebase service module
- admin/common models, services, controllers, migrations, views, routes, and middleware
- `config/devforge-ui.php` for dynamic app name, primary color, secondary color, panel title, and panel description

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

Future Laravel options can be added under `recipes/`, for example:

- `api`
- `blade`
- `inertia`
- `docker`

Use `hooks/` for Laravel-specific generation steps such as installing Composer packages, copying `.env.example`, or running setup commands.
