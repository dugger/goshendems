# Architecture — Goshen Democrats

Local-only reference. Based on inventory of `http://goshen-democrats.local` (recent production restore).

---

## System overview

```
┌─────────────────────────────────────────────────────────────┐
│  Browser → http://goshen-democrats.local                    │
└───────────────────────────┬─────────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────────┐
│  Local WP (nginx + PHP 8.4.10 + MySQL 8.0.35)               │
│  WordPress 7.0                                              │
├─────────────────────────────────────────────────────────────┤
│  Theme: goshendems (symlinked from this git repo)             │
│  Content: ACF Pro field groups + JSON sync                  │
│  Forms: Ninja Forms                                         │
│  SEO: Yoast SEO                                             │
│  Calendar: Simple Calendar (google-calendar-events)         │
└───────────────────────────┬─────────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────────┐
│  Cursor IDE ← MCP HTTP → MCP Adapter plugin                 │
│  Theme edits ← direct file access (symlink)                 │
└─────────────────────────────────────────────────────────────┘
```

---

## Request flow (typical page)

1. WordPress resolves URL via rewrite rules / template hierarchy.
2. Theme `header.php` outputs `<head>`, hardcoded primary nav, Typekit CSS.
3. `wp_head()` runs — Yoast meta, OpenGraph from `inc/template-functions.php`, enqueued assets.
4. Page template (e.g. `page-home.php`) or archive (`archive-story.php`) loads ACF fields.
5. Template parts render sections (`template-parts/content-block-*.php` for stories).
6. `footer.php` closes layout.

---

## URL map (local)

| URL | Renders via | Notes |
|-----|-------------|-------|
| `/` | `page-home.php` | Front page (page ID 14, slug `home`) |
| `/about/` | `page-about.php` | Page ID 76 |
| `/calendar/` | `page-calendar.php` | Page ID 19; Simple Calendar |
| `/contact-us/` | `page-contact-us.php` | Page ID 100; Ninja Form ID 1 |
| `/stories/` | `archive-story.php` | Story CPT archive |
| `/story/{slug}/` | `single-story.php` | **Canonical** single story URL |
| `/stories/{slug}/` | 301 → `/story/{slug}/` | Non-canonical; redirects |

**Note:** Archive slug is `stories` but single permalinks use `/story/…`. Nav links to `/stories/` for the archive — correct. Yoast canonical uses `/story/{slug}/`.
| `/elected-positions/` | `archive-elected-positions.php` | CPT currently **inactive** in ACF |

**Note:** Page ID 54 (`stories`) exists and is set as `Posts page` in WP settings, but `/stories/` renders the **story CPT archive** (`archive-story.php`). The native Posts page setting is misleading — site content uses the `story` CPT, not `post`. Consider clearing the Posts page setting in admin.

---

## Active plugins (local)

| Plugin | Slug | Role |
|--------|------|------|
| Advanced Custom Fields PRO | `advanced-custom-fields-pro` | Content model, JSON sync |
| Ninja Forms | `ninja-forms` | Contact form |
| Yoast SEO | `wordpress-seo` | SEO metadata, sitemaps |
| ACF Content Analysis for Yoast SEO | `acf-content-analysis-for-yoast-seo` | ACF + Yoast integration |
| Auto Focus Keyword for SEO | `auto-focus-keyword-for-seo` | Yoast helper |
| Simple Calendar | `google-calendar-events` | Events on calendar page |
| Classic Editor | `classic-editor` | Disables block editor |
| Cloudflare | `cloudflare` | CDN/cache integration |
| WP Super Cache | `wp-super-cache` | Page caching |
| EWWW Image Optimizer | `ewww-image-optimizer` | Image compression |
| WP Mail SMTP | `wp-mail-smtp` | Outbound email |
| Git Updater | `git-updater` | Theme updates from GitHub |
| All-in-One WP Migration | `all-in-one-wp-migration` | Backups/migration |
| Crop Thumbnails | `crop-thumbnails` | Custom crop sizes |
| DreamHost Panel Login | `dreamhost-panel-login` | Host panel SSO |
| MCP Adapter | `mcp-adapter` | Cursor MCP (local dev) |

Inactive bundled themes: Twenty Twenty-Three through Twenty Twenty-Five.

---

## Theme structure (high level)

```
goshendems/
├── acf-json/              # ACF sync: field groups, CPTs, taxonomy
├── assets/                # Logos, hamburger icon
├── inc/                   # template-functions.php (OG tags), customizer, jetpack
├── js/                    # navigation.js (enqueued), elected-positions-filter.js (orphan)
├── template-parts/        # Reusable partials, content-block-* for stories
├── page-*.php             # Page templates (matched by slug)
├── archive-*.php          # CPT archives
├── single-story.php       # Story single
├── functions.php          # Setup, enqueues, story archive query, ACF toolbar
├── header.php / footer.php
└── style.css              # Main stylesheet (~1500 lines)
```

---

## Content types

| Type | Slug | Count (local) | Defined in |
|------|------|---------------|------------|
| Page | `page` | 5 published | WordPress |
| Story | `story` | 22+ | `acf-json/post_type_68e4df30960d2.json` |
| Elected Position | `elected-positions` | TBD | `acf-json/post_type_693461d475a9e.json` (**inactive**) |
| Taxonomy `level` | on `elected-positions` | TBD | `acf-json/taxonomy_69347cdeefd62.json` |

See [content-model.md](content-model.md) for field-level detail.

---

## WordPress settings (local)

| Setting | Value |
|---------|--------|
| Site title | Goshen Democrats |
| Tagline | *(empty)* |
| Front page | Page ID 14 (`home`) |
| Posts page | Page ID 54 (`stories`) |
| Permalink structure | *(default — verify in Settings → Permalinks)* |
| Timezone | *(verify in admin)* |
| Environment type | `local` |

---

## External services

| Service | Usage |
|---------|--------|
| Adobe Typekit | Fonts via `use.typekit.net/mvz2bfz.css` in `header.php` |
| ActBlue | Donate link in nav (`secure.actblue.com`) |
| Cloudflare Turnstile | Ninja Forms spam protection |
| GitHub | Theme source (`dugger/goshendems`) via Git Updater |

---

## Related docs

- [local-environment.md](local-environment.md) — paths, MCP, WP-CLI
- [content-model.md](content-model.md) — ACF fields and pages
- [theme-guide.md](theme-guide.md) — template and code conventions
- [plugins.md](plugins.md) — plugin integration detail
