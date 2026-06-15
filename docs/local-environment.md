# Local Environment — Goshen Democrats

How to run, connect to, and safely develop against the local WordPress site.

---

## Prerequisites

- [Local WP](https://localwp.com/) app installed
- Site **Goshen Democrats** running (green/Started in Local)
- This theme repo cloned to `/Users/alexdugger/workspace/sites/goshendems`
- Node.js 18+ (for MCP HTTP proxy only)

---

## Paths

| What | Path |
|------|------|
| Local site root | `/Users/alexdugger/Local Sites/goshen-democrats/app/public` |
| Theme (active) | `.../wp-content/themes/goshendems` |
| Theme (git repo) | `/Users/alexdugger/workspace/sites/goshendems` |
| Theme link type | **Symlink** — repo edits are live immediately |
| ACF JSON sync | `{theme}/acf-json/` |
| MCP credentials | `{theme}/.cursor/mcp.json` (**gitignored**) |
| mu-plugin (MCP abilities) | `wp-content/mu-plugins/enable-core-abilities-mcp.php` |

---

## URLs

| URL | Purpose |
|-----|---------|
| http://goshen-democrats.local/ | Front-end |
| http://goshen-democrats.local/wp-admin/ | Admin |
| http://goshen-democrats.local/wp-json/mcp/mcp-adapter-default-server | MCP HTTP endpoint |

---

## WP-CLI

**Do not use Homebrew `wp` from a regular terminal** against this site — it fails with "Error establishing a database connection" because Homebrew PHP does not use Local's MySQL socket.

### Correct approach: Local Site Shell

1. Open **Local** → **Goshen Democrats**
2. Click **Site Shell**
3. Run WP-CLI commands without `--path` (already in WordPress root):

```bash
wp core version
wp plugin list
wp post list --post_type=story
wp user list
```

### STDIO MCP (alternative to HTTP)

From Site Shell:

```bash
wp mcp-adapter serve \
  --server=mcp-adapter-default-server \
  --user=alex
```

Use login name `alex`, not email address.

---

## Cursor MCP setup

Config file: `.cursor/mcp.json` in this repo (see example in repo; credentials are local-only).

```json
{
  "mcpServers": {
    "wordpress-goshen-local": {
      "command": "npx",
      "args": ["-y", "@automattic/mcp-wordpress-remote@latest"],
      "env": {
        "WP_API_URL": "http://goshen-democrats.local/wp-json/mcp/mcp-adapter-default-server",
        "WP_API_USERNAME": "alex",
        "WP_API_PASSWORD": "<Application Password>"
      }
    }
  }
}
```

### Application Password

Create in **Users → Profile → Application Passwords** (not your login password).

Verify auth:

```bash
curl -s "http://goshen-democrats.local/wp-json/wp/v2/users/me" \
  -u "alex:$(python3 -c "import json; print(json.load(open('.cursor/mcp.json'))['mcpServers']['wordpress-goshen-local']['env']['WP_API_PASSWORD'])")"
```

Success returns JSON with `"slug": "alex"`.

Enable in **Cursor → Settings → Tools & MCP**. Server appears as project-scoped (e.g. `project-0-goshendems-wordpress-goshen-local`).

---

## Local MySQL (reference)

Local runs MySQL on a site-specific socket (not system MySQL):

- Site ID folder: `ctWeGQGzF`
- Socket: `~/Library/Application Support/Local/run/ctWeGQGzF/mysql/mysqld.sock`
- Port: `10074` (TCP may be restricted; socket works)

---

## Development workflow

1. Start site in Local.
2. Edit theme files in this repo (symlinked).
3. Refresh browser to see PHP/template changes.
4. ACF field changes: edit in WP admin → sync to JSON, or edit JSON → sync in admin.
5. Use Cursor MCP for site queries (forms, SEO scores, site info).
6. Test forms — Local includes **Mailpit** for catching outbound email.

---

## Git Updater

Theme updates are pulled from GitHub (`dugger/goshendems`). After pushing theme changes:

- Git Updater can detect updates on environments where it is configured.
- Local symlink means git pull in this repo **is** the theme update locally.

---

## Do not

- Commit `.cursor/mcp.json`
- Run Homebrew `wp` against Local without Local's PHP (`WP_CLI_PHP` workaround is fragile)
- Assume page IDs in ACF JSON match other environments after a fresh import
- Install MCP Adapter on production until explicitly planned

---

## Troubleshooting

| Problem | Fix |
|---------|-----|
| Site not loading | Start site in Local app |
| MCP 401 | Regenerate Application Password; use username `alex` |
| MCP tools missing in Cursor | Restart Cursor; check Tools & MCP enabled |
| ACF "Sync available" | Custom Fields → Sync JSON to DB or DB to JSON |
| Theme changes not visible | Confirm symlink: `ls -la wp-content/themes/goshendems` |
