# Language Template Structure

Each supported coding language or framework lives in its own folder under `languages/`.

## Required files

- `manifest.json`: metadata that tells the CLI how to display and generate the template.
- `README.md`: maintainer notes for this language/framework.
- `templates/`: project files that should be copied into the generated output project.

## Optional folders

- `hooks/`: scripts or instructions for steps such as dependency installation, cleanup, or project renaming.
- `recipes/`: named presets for variations such as API-only, full-stack, auth-enabled, or Docker-ready.

## Example

```text
languages/laravel/
  manifest.json
  README.md
  features/
    manifest.json
    <feature-id>/
      app/
      config/
      database/
      resources/
      routes/
  templates/
  hooks/
  recipes/
```

When the CLI is built, it can read `manifest.json`, copy files from `templates/`, apply selected `recipes/`, and run lifecycle hooks.

## Current create behavior

The current `create` command validates that the requested language/framework folder exists, creates the project folder inside the configured output directory, copies files from the selected template, replaces project placeholders, and runs the configured package install step when the template requires it.

Example:

```bash
devforge create blog-api --lang laravel
```

Node.js result:

```text
DevMerge/
  blog-api/
    src/
    tests/
    package.json
    README.md
```

Use `--skip-install` while testing generation without installing packages:

```bash
devforge create blog-api --lang node --skip-install
```

## Laravel behavior

Laravel uses a Composer-based generator instead of copying a full application from `templates/`.

The Laravel generator:

- checks that PHP is installed and meets the Laravel 10 minimum version
- checks that Composer is installed
- asks for database connection and credentials one by one
- validates the actual database connection before Composer install/setup continues
- asks for timezone and updates `config/app.php`
- asks for confirmation before running Composer commands
- creates a Laravel 10 project
- installs required Laravel packages
- publishes package configuration where supported
- configures the JWT `api` guard in `config/auth.php`
- configures package providers and aliases in `config/app.php`
- updates `app/Exceptions/Handler.php` with starter API and JWT exception handling
- updates `RouteServiceProvider` to load `routes/api/v1/api.php`
- removes the default `routes/api.php`
- generates the JWT secret
- creates the storage link
- clears Laravel caches
- applies root `.htaccess`, root `index.php`, `.env`, and `.env.example` setup
- creates `app/Exports/SampleExport.php` when the Excel Export Feature is selected
- asks grouped feature questions for project structure, API, admin, middleware, Firebase, reporting, PDF, and PWA setup
- registers selected helper files in Composer autoload when a real Laravel project is installed
- creates public asset folders for CSS, images, JavaScript, and fonts
- creates base app folders for Constants, Jobs, Listeners, Mails, and Notifications

Required packages are defined in `languages/laravel/manifest.json`.

Laravel feature metadata and generated feature files live in `languages/laravel/features/`.

- `languages/laravel/features/manifest.json` defines the feature list, descriptions, default create-time selections, base folders, env keys, copied files, and route snippets.
- `languages/laravel/features/<feature-id>/` stores the actual PHP, Blade, config, migration, route, and service files copied into generated projects.
- `languages/laravel/features/crud/stubs/` stores reusable CRUD stubs. The CLI builds one module naming definition from the requested module name and fields, then renders model, service, controller, migration, Blade views, routes, and sidebar links from those stubs.
- `src/cli.js` should orchestrate prompts, validation, copying, and setup commands. It should not store long Laravel PHP or Blade templates directly.
- Full reusable Laravel modules should be added as feature packs, such as `common-core`, instead of being hardcoded in the CLI.

Because Laravel 10 is an older major version, modern Composer versions may block dependency resolution when security advisories affect the Laravel 10 dependency range. This generator sets `audit.block-insecure=false` inside the generated Laravel project for the requested Laravel 10 install path and prints a security notice before Composer commands run. Review dependencies before production deployment.

Laravel database setup is intentionally interactive for normal use:

```text
Enter DB Connection [mysql/pgsql/sqlite/sqlsrv]: mysql
Enter DB Host: 127.0.0.1
Enter DB Port: 3306
Enter DB Database Name: clinic_api
Enter DB Username: root
Enter DB Password:
```

Use `--yes` only when you intentionally want Composer install/setup commands to run without an interactive confirmation prompt. Database values are still collected and validated before install/setup continues.

Starter prompts use user-friendly grouped feature names:

```text
Add Project Structure feature?
Add API Feature?
Add Admin Panel Feature?
Add Middleware Feature?
Add Firebase Feature?
Add Excel Export Feature?
Add PDF Export Feature?
Add PWA Feature?
```

The PWA manifest uses the generated project name for `name`, `short_name`, and `description`. The service worker cache uses the generated project slug, for example `clinic-api-cache`.
