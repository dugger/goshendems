# AGENTS.md — Goshen Democrats WordPress Site

**Read this file first** before editing the theme, content model, or local WordPress configuration.

This repository is the **goshendems** custom WordPress theme for the Goshen City Democratic Party website. The local development site is a recent restore of production and is the authoritative environment for this audit and documentation effort.

---

## Quick reference

| Item | Value |
|------|--------|
| Site name | Goshen Democrats |
| Local URL | http://goshen-democrats.local |
| WordPress | 7.0 |
| PHP | 8.4.10 |
| Theme | `goshendems` v1.3.1 |
| Theme repo | This directory |
| GitHub | https://github.com/dugger/goshendems |
| Local WP root | `/Users/alexdugger/Local Sites/goshen-democrats/app/public` |
| Theme in Local | Symlink → this repo |

---

## Documentation map

Read the relevant doc **before** making changes:

| Task | Read first |
|------|------------|
| Understand stack, URLs, plugins | [docs/architecture.md](docs/architecture.md) |
| Local setup, MCP, WP-CLI | [docs/local-environment.md](docs/local-environment.md) |
| Pages, CPTs, ACF fields | [docs/content-model.md](docs/content-model.md) |
| Templates, partials, PHP conventions | [docs/theme-guide.md](docs/theme-guide.md) |
| Ninja Forms, Yoast, calendar, etc. | [docs/plugins.md](docs/plugins.md) |
| User flows and UX behavior | [docs/user-journeys.md](docs/user-journeys.md) |
| Add a story content block | [docs/how-to/add-story-block.md](docs/how-to/add-story-block.md) |
| Add a page template | [docs/how-to/add-page-template.md](docs/how-to/add-page-template.md) |
| Add an ACF field group | [docs/how-to/add-acf-field-group.md](docs/how-to/add-acf-field-group.md) |
| Add a custom post type | [docs/how-to/add-cpt.md](docs/how-to/add-cpt.md) |
| Audit findings & known issues | [docs/audit/findings.md](docs/audit/findings.md) |
| **Prioritized fix backlog (start here for todos)** | [docs/audit/summary.md](docs/audit/summary.md) |

---

## Stack summary

- **WordPress 7.0** with **Classic Editor** (block editor not used for primary content)
- **ACF Pro** — field groups, CPTs, and taxonomies synced via `acf-json/`
- **Custom theme** — Underscores-based; ACF-driven page templates and story flexible content
- **Ninja Forms** — Contact Us form (ID 1), Cloudflare Turnstile spam protection
- **Yoast SEO** — SEO and readability scoring
- **Simple Calendar** — Google Calendar Events plugin; calendar page
- **WP Super Cache**, **Cloudflare**, **EWWW Image Optimizer**, **WP Mail SMTP**
- **Git Updater** — theme updates from GitHub
- **MCP Adapter** — local dev only; connects Cursor to WordPress via `.cursor/mcp.json`

---

## Conventions for AI agents

### Theme code

- Match escaping patterns in sibling templates (`esc_html`, `esc_url`, `esc_attr`, `wp_kses_post` for WYSIWYG).
- New story flexible content layouts require **ACF JSON + template partial + CSS** — see [docs/how-to/add-story-block.md](docs/how-to/add-story-block.md).
- Prefer **page template** or **page slug** ACF location rules over hardcoded page IDs.
- Do not hardcode Ninja Form IDs in templates when an ACF field exists for the shortcode.
- Enqueue new JS/CSS in `functions.php` via `goshendems_scripts()` — do not add orphan script files.
- Run PHPCS before committing PHP changes (`composer phpcs` — run `composer install` first).

### ACF

- Field group changes belong in `acf-json/` and sync through WP admin (Custom Fields → Sync).
- CPT and taxonomy definitions live in `acf-json/post_type_*.json` and `acf-json/taxonomy_*.json`.

### Local development

- Edit theme files **in this repo** — Local uses a symlink; changes appear immediately.
- Use **Local Site Shell** for WP-CLI, not Homebrew `wp` (database socket mismatch).
- Use **MCP tools** (`wordpress-goshen-local` in Cursor) for site info, forms, and Yoast data.
- Never commit `.cursor/mcp.json` (contains Application Password).

### Out of scope (for now)

- Production deployment, Cloudflare prod rules, production MCP
- Changing production database or live URLs

---

## MCP tools available (local)

Cursor MCP server: `wordpress-goshen-local` (project-scoped)

Meta tools (always available):

- `mcp-adapter-discover-abilities`
- `mcp-adapter-get-ability-info`
- `mcp-adapter-execute-ability`

Notable abilities:

- `core/get-site-info`, `core/get-user-info`, `core/get-environment-info`
- `ninjaforms/*` — forms, fields, submissions, settings
- `yoast-seo/get-seo-scores`, `yoast-seo/get-readability-scores`

---

## Known issues & fix backlog

**46 open findings** — full log in [docs/audit/findings.md](docs/audit/findings.md).

**Prioritized todo list** — [docs/audit/summary.md](docs/audit/summary.md) (Tier 1 quick wins → Tier 5 deferred).

Top items to address first:

1. Remove home page JS error + fix unclosed `<main>`
2. Remove duplicate OG tags (Yoast owns social meta)
3. Fix broken `page.php`
4. Update stale "building this site" copy
5. Escaping pass on templates
6. Elected Positions deferred to separate project (not audit scope)

---

## Audit status

**Complete** — Phases 0–6 finished June 15, 2026.

| Phase | Status |
|-------|--------|
| Phase 0 — Local inventory & doc scaffold | Complete |
| Phase 1 — Theme code audit | Complete |
| Phase 2 — ACF & content model | Complete |
| Phase 3 — Plugins | Complete |
| Phase 4 — Security | Complete |
| Phase 5 — Usability | Complete |
| Phase 6 — Doc assembly | Complete |

- **Summary & backlog:** [docs/audit/summary.md](docs/audit/summary.md)
- **All findings:** [docs/audit/findings.md](docs/audit/findings.md)
- **Changelog:** [docs/audit/changelog.md](docs/audit/changelog.md)
