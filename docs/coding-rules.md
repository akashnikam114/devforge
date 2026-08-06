# Coding Rules

These rules must be followed while developing DevForge.

## Safety

- Always ask permission before running sensitive work.
- Sensitive work includes deleting files, overwriting generated projects, installing dependencies, changing git history, pushing code, publishing packages, or running commands that affect files outside this repository.
- Prefer a clear explanation before any command that has side effects beyond normal local code editing.

## Clean Code

- Keep the code clean, readable, and focused on the current step.
- Avoid unrelated refactoring while implementing a specific feature.
- Use meaningful names for commands, config keys, files, and console messages.
- Keep console output descriptive enough that users understand what happened and what to do next.
- Every CLI command must print clear, descriptive, and meaningful console messages for start, progress, success, failure, and next steps where applicable.
- After testing commands that create temporary files or folders, delete those test-created files or folders once verification is complete.

## Structure

- Follow the standard project structure documented in `README.md`.
- Keep CLI source code inside `src/`.
- Keep executable command entry points inside `bin/`.
- Keep language/framework template support inside `languages/<name>/`.
- Keep project documentation inside `docs/`.

## Documentation

- After each implementation step, update documentation wherever the behavior, command, config, or structure changes.
- Keep examples current with the actual CLI behavior.
- Document configuration values that users may want to rename later.

## Command Naming

- Keep the CLI command name configurable through `config/devforge.json`.
- After changing `commandName`, run `npm run sync-command` so `package.json` uses the same executable command name.
- Re-link or reinstall the package after changing the executable command name.
