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

## File Links In Responses

- In this repo context, prefer bare absolute filesystem paths in prose instead of Markdown file links when you want the app to auto-link a file.
- Use literal spaces, not URL-encoded paths.
- Preferred: /Users/ben/Local Sites/ben-montgomery/app/public/wp-content/themes/ben-montgomery/theme.json
- Avoid relying on Markdown file-link rendering here.
