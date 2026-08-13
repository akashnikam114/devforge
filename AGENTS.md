# DevForge Agent Rules

These instructions apply to the whole repository.

## Project Context

DevForge is a Node.js CLI for generating starter templates for multiple languages and frameworks. Keep CLI source in `src/`, executable entry points in `bin/`, config in `config/`, docs in `docs/`, and framework templates in `languages/<name>/`.

## Required Rules

- Follow `docs/coding-rules.md` for all development work.
- Keep the command name configurable through `config/devforge.json`.
- After changing `config/devforge.json > commandName`, run `npm run sync-command`.
- Update documentation whenever behavior, commands, config, or project structure changes.
- Keep console output clear for command start, progress, success, failure, and next steps.
- Avoid unrelated refactors.
- Do not overwrite generated projects or delete test-created files without explicit confirmation.

## Local Commands

- Run the CLI directly: `./bin/devforge --help`
- Run through npm: `npm run devforge -- --help`
- Sync command name: `npm run sync-command`
- Link globally after syncing: `npm link`

Generated projects are created inside `DevMerge/` by default and are ignored by git.
