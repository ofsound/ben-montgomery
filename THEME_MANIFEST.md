# Ben Montgomery Theme Manifest

## Purpose

This repository is the authoritative source for the Ben Montgomery WordPress theme. WordPress core, the Local site root, and uploads are runtime concerns and do not belong in this repository.

## Architecture Rules

- Use a block theme with file-based templates, template parts, patterns, and `theme.json`.
- Put design tokens, spacing, typography, layout, color, and block support decisions in `theme.json` before touching CSS or PHP.
- Keep `functions.php` minimal. Only add behavior that cannot live in `theme.json`, `block.json`, or block markup.
- Build blog layouts with core `Query Loop` and `Post Template`.
- Use ACF Pro only for components that truly need structured fields or editor guardrails that native blocks cannot provide cleanly.
- Save ACF field groups to `acf-json/` and never rely on database-only field definitions.
- Treat Site Editor database changes as temporary until they are synced back into files.

## Content Model

- `post`: blog posts and editorial content.
- `page`: Home, About, Contact, and other standard marketing pages.
- No custom post types in v1.

## Local Workflow

- Persistent site: Local app + this WordPress install.
- Disposable experiments: direct `@wp-playground/cli` from this theme directory, using the repo-owned blueprint in `playground/blueprint.json`.
- Run WP-CLI through `./bin/wp-local` from the theme repo root.
- Install the repo-managed WordPress MCP loader plugin with `npm run mcp:plugin:install`, then install its Composer deps and activate it.
- Register Codex against the local WordPress MCP server with `codex mcp add wordpress-local -- "/absolute/path/to/bin/wp-local" mcp-adapter serve --server=mcp-adapter-default-server --user=<local-user>`.
- Set the Codex MCP server `cwd` to the theme repo root so `wordpress-local` launches from the verified working context.
- Restart Codex after adding or changing MCP servers so new sessions load the updated shared MCP config.
- Restart Codex after changing the loader plugin or MCP ability exposure rules; existing sessions can keep the old MCP tool/ability snapshot.
- If WP-CLI reports `Error establishing a database connection`, Local's database service is not currently reachable; start the site in Local before running migrations or seed scripts.

## Primary Commands

```bash
npm install
npm run mcp:plugin:install
npm run mcp:plugin:deps
./bin/wp-local plugin activate wordpress-mcp-adapter-loader
./bin/wp-local mcp-adapter list
npm run playground:start
npm run playground:status
npm run playground:stop
./bin/wp-local theme activate ben-montgomery
npm run wp:seed
npm run wp:sync-site-editor
npm run verify
./bin/wp-local plugin list
./bin/wp-local db query "SHOW TABLES LIMIT 5;"
./bin/wp-local search-replace 'old.example.test' 'new.example.test' --skip-columns=guid --dry-run
```

## Codex + MCP Workflow

- Repo-managed plugin source lives in `tools/wordpress-mcp-adapter-loader/`.
- The live site plugin at `wp-content/plugins/wordpress-mcp-adapter-loader` should be a symlink back to that repo-managed source.
- The loader plugin only bootstraps the official `wordpress/mcp-adapter` package and should remain minimal.
- The public MCP surface is intentionally minimal: `ben-montgomery/get-content`, `ben-montgomery/update-content`, `ben-montgomery/sync-site-editor`, and the `wordpress://ben-montgomery/site-map` resource.
- Theme file reads, diagnostics, seeding, search-replace, plugin activation, Playground, and `wp-env` should stay outside MCP and use repo files or `./bin/wp-local` directly.
- Include `--user=<local-user>` when serving or inspecting the MCP server so administrator-scoped tools and resources appear in the inventory.
- Keep `composer.json` and `composer.lock` for the loader plugin version-controlled; do not commit its `vendor/` directory.
- Verify the local WordPress MCP server with `./bin/wp-local mcp-adapter list --user=<local-user>`.

## Site Editor Sync Policy

1. Use the Site Editor for rapid layout iteration on templates and template parts.
2. Run `npm run wp:sync-site-editor` after meaningful editor sessions.
3. Review the exported files in `templates/`, `parts/`, and `styles/`.
4. Promote style exports back into `theme.json` or permanent CSS before commit.
5. Do not leave unsynced template changes in the database.
6. Run `npm run verify` before commit so the Codex-facing MCP workflow is smoke-tested against the live site.

## ACF Policy

- Delay ACF until native blocks or patterns become awkward.
- Prefer block metadata and clear field schemas over flexible-content style sprawl.
- Keep local JSON committed in `acf-json/`.

## Migration and Seeding Notes

- Use `scripts/seed-content.sh` for baseline pages and sample posts.
- Keep future migration scripts in `scripts/` and make them idempotent.
- Prefer WP-CLI commands over manual admin actions for plugin management, DB inspection, and search-replace.

## Playground Notes

- `.wp-env.json` is retained for version pinning/reference, but the authoritative disposable workflow is the direct Playground CLI.
- If the direct Playground server is already listening on port `8888`, `npm run playground:start` should treat that as already running instead of rebuilding anything.
- The disposable Playground site should activate the `ben-montgomery` theme automatically from the mounted repo.
