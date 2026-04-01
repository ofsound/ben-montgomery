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
- Disposable experiments: `wp-env` with the Playground runtime from this theme directory.
- Run WP-CLI through `./bin/wp-local` so commands always target the correct Local site root.
- If WP-CLI reports `Error establishing a database connection`, Local's database service is not currently reachable; start the site in Local before running migrations or seed scripts.

## Primary Commands

```bash
npm install
npm run playground:start
npm run playground:status
npm run playground:stop
npm run wp:theme:activate
npm run wp:seed
npm run wp:sync-site-editor
./bin/wp-local plugin list
./bin/wp-local db query "SHOW TABLES LIMIT 5;"
./bin/wp-local search-replace 'old.example.test' 'new.example.test' --skip-columns=guid --dry-run
```

## Site Editor Sync Policy

1. Use the Site Editor for rapid layout iteration on templates and template parts.
2. Run `npm run wp:sync-site-editor` after meaningful editor sessions.
3. Review the exported files in `templates/`, `parts/`, and `styles/`.
4. Promote style exports back into `theme.json` or permanent CSS before commit.
5. Do not leave unsynced template changes in the database.

## ACF Policy

- Delay ACF until native blocks or patterns become awkward.
- Prefer block metadata and clear field schemas over flexible-content style sprawl.
- Keep local JSON committed in `acf-json/`.

## Migration and Seeding Notes

- Use `scripts/seed-content.sh` for baseline pages and sample posts.
- Keep future migration scripts in `scripts/` and make them idempotent.
- Prefer WP-CLI commands over manual admin actions for plugin management, DB inspection, and search-replace.

