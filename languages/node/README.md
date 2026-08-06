# Node Template

This folder contains Node.js-specific starter template support.

Put Node project files and folders that should be generated into `templates/`.

The current template creates an Express application with:

- environment configuration
- app/server separation
- health route
- error and not-found middleware
- simple logger utility
- built-in Node test runner example
- npm scripts for start, development, and tests
- standard security and request middleware packages

Future Node options can be added under `recipes/`, for example:

- `api`
- `express`
- `typescript`
- `cli`

Use `hooks/` for Node-specific generation steps such as installing packages or creating environment files.
