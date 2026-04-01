# WordPress MCP Adapter Loader

This directory is the repo-managed source for the local WordPress MCP loader plugin.

Setup:

```bash
composer install --working-dir ./tools/wordpress-mcp-adapter-loader
npm run mcp:plugin:install
npm run mcp:plugin:activate
```

The live plugin in `wp-content/plugins/wordpress-mcp-adapter-loader` should be a symlink back to this directory.

The loader keeps the public MCP surface intentionally small for Codex:

- `ben-montgomery/get-content`
- `ben-montgomery/update-content`
- `ben-montgomery/sync-site-editor`
- `wordpress://ben-montgomery/site-map`

Other site-specific abilities remain registered for internal workflows, but are not exposed as public MCP tools, resources, or prompts.

When checking inventory or serving the MCP server for Codex, include `--user=<local-user>` so the administrator-scoped capabilities are visible.
