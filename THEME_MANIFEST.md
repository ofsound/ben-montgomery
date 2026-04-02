# Ben Montgomery Theme Manifest

## Purpose

This repository is the authoritative source for the Ben Montgomery WordPress theme. Permanent theme changes must live in version-controlled files, not in WordPress database state.

## Core Rule

Build and maintain this as a file-first block theme.

- Use `theme.json`, `templates/*.html`, `parts/*.html`, `patterns/`, and CSS as the primary authoring surface.
- Do not use the Site Editor as the main place where the theme is built.
- Do not rely on database-only template, template-part, style, or ACF changes.
- If a change matters, it must exist in a file that can be reviewed in Git.

## Theme Architecture

- Use a block theme with file-based templates, template parts, patterns, and `theme.json`.
- Put design tokens, spacing, typography, colors, layout settings, and block defaults in `theme.json` before adding CSS or PHP.
- Use CSS only for styling that `theme.json` and block markup cannot express cleanly.
- Keep `functions.php` minimal. Add PHP only when the behavior cannot live in `theme.json`, block markup, or block metadata.
- Build blog/archive layouts with core blocks such as `Query Loop` and `Post Template`.
- Use patterns for reusable sections.
- Use ACF Pro only when native blocks and patterns are no longer a clean fit for the content model.
- Save ACF field groups to `acf-json/`. Never rely on database-only field definitions.

## Agent Workflow

- Agents should edit theme files directly.
- Prefer changes in:
  - `theme.json`
  - `templates/*.html`
  - `parts/*.html`
  - `patterns/*.php`
  - theme CSS files
- Agents should not use the Site Editor as a normal implementation tool.
- Agents should not intentionally create database-only theme changes.
- Any experimental Site Editor work is temporary unless it is immediately exported and reconciled back into files.

## Site Editor Policy

- The Site Editor is optional and secondary.
- Use it only for previewing, testing, or quick one-off experiments.
- Do not treat Site Editor state as source of truth.
- Avoid routine use of Site Editor sync in normal development.
- If someone does make template or style changes in the Site Editor and wants to keep them, export them promptly and move the lasting decisions back into `theme.json`, template files, parts, or CSS before commit.

## Content Model

- `post`: blog posts and editorial content.
- `page`: Home, About, Contact, and other standard marketing pages.
- No custom post types in v1.

## Local Workflow

- Persistent site: Local app + this WordPress install.
- Disposable experiments: direct `@wp-playground/cli` from this theme directory, using `playground/blueprint.json`.
- Run WP-CLI through `./bin/wp-local` from the theme repo root.
- If WP-CLI reports `Error establishing a database connection`, Local's database service is not reachable; start the site in Local before running migrations, seeds, or verification.

## Primary Commands

```bash
npm install
npm run mcp:plugin:install
npm run mcp:plugin:deps
./bin/wp-local plugin activate wordpress-mcp-adapter-loader
./bin/wp-local theme activate ben-montgomery
npm run wp:seed
npm run verify
./bin/wp-local plugin list
./bin/wp-local db query "SHOW TABLES LIMIT 5;"
./bin/wp-local search-replace 'old.example.test' 'new.example.test' --skip-columns=guid --dry-run
```

## Optional Site Editor Recovery Commands

Use these only if someone intentionally made Site Editor changes that need to be preserved.

```bash
npm run wp:sync-site-editor
```

After exporting:

- review the files written to `templates/`, `parts/`, and `styles/`
- move lasting style decisions back into `theme.json` or permanent CSS
- do not commit accidental database-driven output without review

## Codex + MCP Notes

- Repo-managed plugin source lives in `tools/wordpress-mcp-adapter-loader/`.
- The live site plugin at `wp-content/plugins/wordpress-mcp-adapter-loader` should be a symlink back to that repo-managed source.
- The loader plugin should remain minimal and only bootstrap the official `wordpress/mcp-adapter` package.
- Theme implementation work should happen in repo files or through `./bin/wp-local`, not through hidden Site Editor state.
- Verify the local WordPress MCP server with `./bin/wp-local mcp-adapter list --user=<local-user>` when needed.

## Migration and Seeding Notes

- Use `scripts/seed-content.sh` for baseline pages and sample posts.
- Keep future migration scripts in `scripts/` and make them idempotent.
- Prefer WP-CLI commands over manual admin actions for plugin management, DB inspection, and search-replace.

## Playground Notes

- `.wp-env.json` is retained for version pinning and reference, but the authoritative disposable workflow is the direct Playground CLI.
- If the direct Playground server is already listening on port `8888`, `npm run playground:start` should treat that as already running instead of rebuilding anything.
- The disposable Playground site should activate the `ben-montgomery` theme automatically from the mounted repo.
