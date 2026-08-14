# DevForge

DevForge is a CLI project for generating starter templates for multiple coding languages and frameworks.

The first supported template target is Laravel. More language or framework templates can be added under `languages/`.

## Project layout

```text
DevForge/
  bin/                 CLI executable entry points
  config/              CLI configuration and defaults
  docs/                Project documentation
  languages/           Template definitions grouped by coding language/framework
  src/                 CLI source code
  tests/               Automated tests
```

## Configuration

User-facing names and paths are controlled from [config/devforge.json](/Users/akashnikam/Documents/DevForge/config/devforge.json).

```json
{
  "appName": "DevForge",
  "commandName": "devforge",
  "defaultOutputDirectory": "DevMerge",
  "languagesDirectory": "languages",
  "languageOptionName": "--lang"
}
```

You can also create a local `.env` file from [.env.example](/Users/akashnikam/Documents/DevForge/.env.example) when you want to rename values without editing the config file. Local `.env` files are ignored by git.

Important: `commandName` controls the command shown by the CLI and the package command name after syncing. If you change `commandName`, run:

```bash
npm run sync-command
```

Then re-link or reinstall the package so your terminal recognizes the new command name.

## Setup After Cloning

After cloning this repository on any machine, the direct executable works immediately:

```bash
./bin/devforge --help
```

To use the configured command name directly, sync and link the package:

```bash
npm run sync-command
npm link
devforge --help
```

If you changed `commandName` in [config/devforge.json](/Users/akashnikam/Documents/DevForge/config/devforge.json), use that new command after linking:

```bash
npm run sync-command
npm link
NEW_COMMAND_NAME --help
```

`npm run sync-command` updates `package.json > bin` from `config/devforge.json > commandName`. `npm link` makes that command available in your terminal.

## Adding language support

Create a new folder inside `languages/`:

```text
languages/<language-or-framework>/
  manifest.json        Template metadata used by the CLI
  README.md            Notes for maintainers
  templates/           Files copied into the generated project
  hooks/               Optional scripts run before/after generation
  recipes/             Optional variations, presets, or stacks
```

Use `languages/_example/` as the reference shape.

## CLI usage

Run the CLI from this repository:

```bash
npm run devforge -- create PROJECT_NAME --lang laravel
```

For direct terminal usage without `./bin`, link the package one time:

```bash
npm link
```

If your system blocks global npm links with a permission error, check the npm prefix:

```bash
npm config get prefix
```

If it returns `/usr/local`, use a user-owned npm global directory:

```bash
mkdir -p ~/.npm-global
npm config set prefix ~/.npm-global
```

Then add this to your shell profile, such as `~/.zshrc`:

```bash
export PATH="$HOME/.npm-global/bin:$PATH"
```

Reload your terminal:

```bash
source ~/.zshrc
```

Then run:

```bash
npm link
```

After linking or installing the package, use:

```bash
devforge --help
devforge create PROJECT_NAME --lang TEMPLATE
devforge create PROJECT_NAME --lang TEMPLATE --skip-install
devforge feature --list
devforge feature PROJECT_NAME
devforge feature PROJECT_NAME --feature FEATURE_ID
devforge crud PROJECT_NAME MODULE_NAME
devforge crud PROJECT_NAME MODULE_NAME --fields name:string,description:text,is_active:boolean
devforge feature PROJECT_NAME --feature firebase --firebase-project PROJECT --firebase-credentials /path/to/service-account.json
devforge feature PROJECT_NAME --feature api-rate-limit --api-limit-per-minute 60 --api-block-duration 3600
devforge feature PROJECT_NAME --feature payment-gateway --payment-provider razorpay --payment-mode pg
```

For Laravel, DevForge checks PHP and Composer before running Composer commands. It then asks for database settings one by one, validates the actual database connection, creates a Laravel 10 project, installs the configured Laravel packages, publishes package config files, creates the storage link, and clears framework caches.

Laravel hosting setup copies root `.htaccess`, root `index.php`, root `server.php`, and `public/.htaccess` from the Laravel template.

After the Laravel project setup starts, DevForge asks for grouped starter features with descriptions, so each setup area is asked only once:

```text
Add Project Structure feature? [yes/no]
Add API Feature? [yes/no]
Add Admin Panel Feature? [yes/no]
Add Admin Auth Feature? [yes/no]
Add Admin UI Theme Feature? [yes/no]
Add Admin Assets Feature? [yes/no]
Add Common Core Modules Feature? [yes/no]
Add App Release Feature? [yes/no] (asked when Common Core is not selected)
Add Admin Panel Activity Log Feature? [yes/no]
Add Users Module Feature? [yes/no]
Add API Rate Limit Feature? [yes/no]
Enter API limit per minute
Enter API block duration in seconds
Add Payment Gateway Feature? [yes/no]
Select payment gateway provider [easebuzz/razorpay/phonepe]
Select payment gateway mode [autopay/pg/both]
Add Middleware Feature? [yes/no]
Add Firebase Feature? [yes/no]
Add Excel Export Feature? [yes/no]
Add PDF Export Feature? [yes/no]
Add PWA Feature? [yes/no]
```

The Laravel starter also asks for timezone and updates `config/app.php`. Default timezone is `Asia/Kolkata`.

RBAC is always included for Laravel. It creates:

- `roles` table with `id`, `name`, `description`, and timestamps
- `Super Admin` starter role
- enriched `users` table with nullable email, phone number, `role_id` foreign key, status, UTM/device fields, and soft deletes
- starter admin user using `admin@example.com` and `Admin@123`
- `user_otps` table and `UserOtp` model
- `app/Models/Role.php`
- `role()` relation on `app/Models/User.php` when that model is available

JWT environment defaults include `JWT_TTL=30` and `JWT_REFRESH_TTL=43200`, which means 30 minutes and 30 days.

Laravel queue jobs are database-backed by default. DevForge writes `QUEUE_CONNECTION=database` and adds the `jobs` table migration.

API request logging uses the `api_logs` daily log channel in `config/logging.php` and keeps seven days of logs at `storage/logs/api.log`.

The Excel setup creates `app/Exports/SampleExport.php` for `maatwebsite/excel` when the Excel Export Feature is selected.

The Common Core Modules Feature is the full reusable admin starter pack. It adds admin auth, dashboard/layout views, admin assets, BusinessSetting, GeneralSetting, AppRelease, restriction settings, banners, push notifications, helpers, encryption helper, Firebase service, models, services, migrations, middleware, storage folders, and routes. Admin CSS, JavaScript, and fonts are bundled from the DashLite demo1 theme and copied to `public/assets/admin`; admin images live under `public/assets/admin/images` and include a sidebar-ready `app-logo.png`, `default-image.png`, and `favicons/favicon.ico`; PWA uses `public/pwa/app-icon.png`. The admin shell keeps DashLite sidebar spacing/hover behavior, uses DataTables' native processing loader, and lets Business Settings upload an app logo for generated projects. When UI theme setup is selected, DevForge asks for app name, primary color, secondary color, panel title, and panel description, then writes `APP_UI_*` values to `.env`, `.env.example`, and `config/app-ui.php`.

The Admin Panel Activity Log Feature adds Spatie Activitylog setup, login/logout listeners, admin request activity middleware, an `activity_log` table, and an Activity Logs admin page.

The API Rate Limit Feature adds `ApiRateLimitGuard` as a separate API middleware. It writes `API_RATE_LIMIT_PER_MINUTE` and `API_RATE_LIMIT_BLOCK_DURATION_SECONDS` to `.env` and `.env.example`.

The Users Module Feature adds an admin users listing and details page based on DevForge's default `users` table.

The Payment Gateway Feature supports `easebuzz`, `razorpay`, and `phonepe`. Easebuzz and PhonePe can generate `autopay`, `pg`, or `both` services. Razorpay generates PG scaffolding only and includes `razorpay_webhooks` storage plus webhook endpoints.

Admin UI setup applies the selected theme color to buttons, header/profile bar states, dropdown actions, form focus states, sidebar menu states, pagination, badges, dashboard card accents, and Select2 focus/highlight states. General Settings uses Select2 for language, date format, and time format. Business Settings uses Select2 for currency and OTP provider, while sensitive values such as `encryption_key` remain stored but are not shown in the admin panel.

API normalization middleware accepts app request payloads in `camelCase`, converts them to `snake_case` for Laravel processing, and converts JSON responses back to `camelCase`.

Laravel middleware setup registers secure headers globally, keeps API request normalization/logging/sanitization in the API middleware group, and registers `api.auth`, `app.maintenance`, and `admin.maintenance` aliases for route usage. `AppServiceProvider` shares the common `appSetting` helper with all Blade views.

Firebase setup asks for a Firebase project name, then asks you to drag/drop or paste the uploaded service-account `.json` file path into the terminal. DevForge copies the JSON to `storage/app/public/{firebase-project-name}-firebase-adminsdk.json` and writes `FIREBASE_PROJECT_ID` plus `FIREBASE_CREDENTIALS`.

Selected starter structure creates:

- `app/Helpers/helpers.php`
- `app/Services/.gitkeep`
- `app/Traits/.gitkeep`
- `public/pwa/`
- `public/assets/web/css/.gitkeep`
- `public/assets/web/images/.gitkeep`
- `public/assets/web/js/.gitkeep`
- `public/assets/web/fonts/.gitkeep`
- `storage/app/public/Others/`
- `storage/app/public/Restriction/Maintenance_Mode.png`

Base Laravel structure always includes:

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

In full Laravel installs, DevForge removes the default `routes/api.php` file and updates `RouteServiceProvider` to load `routes/api/v1/api.php`.

Laravel route files keep package imports and controller imports at the top of the file. Generated web/API routes use imported controller class names instead of inline fully qualified controller names.

## Adding skipped Laravel features later

List available Laravel features:

```bash
devforge feature --list
```

Interactively add a skipped feature:

```bash
devforge feature blog-api
```

Add one feature directly:

```bash
devforge feature blog-api --feature admin-auth
```

The interactive command shows numbered features, asks which feature to apply, reports progress, and then asks whether you want to add another feature.

## Generating Laravel CRUD modules

Use the CRUD command after the Admin Panel or Common Core feature exists in the generated Laravel project:

```bash
devforge crud blog-api Category
devforge crud blog-api ProductType --fields name:string,description:text,is_active:boolean
```

DevForge defines the module naming once, then uses that definition everywhere:

- model: `Category`
- controller: `CategoryController`
- service: `CategoryService`
- table: `categories`
- route: `admin/categories`
- views: `resources/views/admin/categories`

Supported field types are `string`, `text`, `integer`, `decimal`, `boolean`, `date`, `datetime`, `email`, and `url`.

The generated CRUD includes model, service, admin controller, migration, list/add/edit Blade views, form partial, admin routes, DataTables integration, delete action, optional `is_active` status toggle, and sidebar link. If `--fields` is not provided, DevForge uses `name:string,description:text,is_active:boolean`.

If you intentionally want to regenerate the same module files, use:

```bash
devforge crud blog-api Category --force
```

Use `--skip-install` when testing folder/file generation only.

Laravel interactive flow:

```text
Enter DB Connection [mysql/pgsql/sqlite/sqlsrv]: mysql
Enter DB Host: 127.0.0.1
Enter DB Port: 3306
Enter DB Database Name: clinic_api
Enter DB Username: root
Enter DB Password:
```

DevForge will not continue until the database connection is valid.

## Development rules

See [docs/coding-rules.md](/Users/akashnikam/Documents/DevForge/docs/coding-rules.md).
