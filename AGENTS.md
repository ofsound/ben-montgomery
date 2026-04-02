# Repo Instructions

## Local WordPress Runtime

- Treat `bin/wp-local` as a live Local-app command, not a pure workspace command.
- Run Local-backed WP-CLI commands outside the sandbox by default.
- Use the approved command prefix `wp-content/themes/ben-montgomery/bin/wp-local` when the current working directory is `/Users/ben/Local Sites/ben-montgomery/app/public`.
- Do not infer that Local is stopped just because sandboxed `wp-local` or MySQL access fails. The sandbox may be unable to reach Local's private MySQL socket or port even while the site is running normally.

## When To Use `wp-local`

- Use `bin/wp-local` for theme status, plugin status, option reads, DB queries, migrations, seeding, and any other command that depends on live WordPress state.
- Use normal sandboxed file reads for theme source inspection such as `theme.json`, templates, patterns, CSS, and PHP files.

## Diagnostics

- If `wp-local` fails in the sandbox, treat that as an execution-context problem first.
- Confirm the Local site mapping from `~/Library/Application Support/Local/sites.json` and the generated runtime config under `~/Library/Application Support/Local/run/<site-id>/` before concluding that WordPress or MySQL is down.

## Block Markup Safety

- In `templates/*.html`, `parts/*.html`, and block-based pattern markup, do not hand-edit extra attributes onto core block wrapper HTML.
- Do not add custom `data-*`, ARIA, `role`, or other non-serialized attributes directly inside markup emitted by core blocks such as `core/group`, `core/button`, `core/navigation`, and similar.
- If a behavior needs custom attributes or runtime state, add it with CSS, JavaScript, block metadata, or a custom block, not by mutating core block wrapper markup in theme files.
- Before considering a template-part file "safe", ensure it round-trips through WordPress parsing and serialization without changing. Footer-safe is the standard.
- If a template part shows "unexpected or invalid content" after reset, first suspect hand-written wrapper markup drift before inventing a custom block workaround.

## File Links In Responses

- In this repo context, prefer bare absolute filesystem paths in prose instead of Markdown file links when you want the app to auto-link a file.
- Use literal spaces, not URL-encoded paths.
- Preferred: /Users/ben/Local Sites/ben-montgomery/app/public/wp-content/themes/ben-montgomery/theme.json
- Avoid relying on Markdown file-link rendering here.
