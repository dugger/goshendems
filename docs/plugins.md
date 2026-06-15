# Plugins — Goshen Democrats

How installed plugins integrate with the theme. Audited on local restore (Phase 3).

---

## ACF Pro (`advanced-custom-fields-pro`)

**Role:** Entire content model — field groups, Story CPT, Elected Positions CPT, `level` taxonomy.

**Theme integration:**

- JSON sync directory: `{theme}/acf-json/`
- Page templates call `get_field()` / `the_field()`
- Custom WYSIWYG toolbar via `acf/fields/wysiwyg/toolbars` filter in `functions.php`
- Story flexible content drives `template-parts/content-block-*.php`
- ACF Content Analysis for Yoast bridges fields into SEO analysis

**Agent notes:**

- Sync JSON after field changes
- Do not register CPTs in PHP — they live in ACF JSON
- See [content-model.md](content-model.md)

---

## Ninja Forms (`ninja-forms`) v3.14.6

**Role:** Contact form on Contact Us page.

### Contact Us form (ID 1)

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| name | textbox | yes | — |
| email | email | yes | `personally_identifiable: true` |
| join_our_mailing_list | checkbox | no | Default checked |
| phone | phone | no | `personally_identifiable: true` |
| message | textarea | no | — |
| submit | submit | — | — |
| turnstile | Cloudflare Turnstile | — | Label invisible |

### Actions (all active)

| Action | Purpose |
|--------|---------|
| Record Submission | Saves to database |
| Email Confirmation | Auto-reply to `{field:email}` from `goshendemocraticparty@gmail.com` |
| Email Notification | Admin alert to `goshendemocraticparty@gmail.com` |
| Success Message | On-screen confirmation with merge tags |

**Submissions (local):** 42 total on form 1.

### Global settings

| Setting | Value |
|---------|--------|
| Spam protection | Cloudflare Turnstile (configured) |
| reCAPTCHA / hCaptcha | Not configured |
| Date format | m/d/Y |
| Currency | USD |

**Agent notes:**

- Turnstile site/secret keys live in WP options — never commit to repo
- `name` field lacks `personally_identifiable` flag (email/phone have it)
- Prefer ACF `form_shortcode` over hardcoded `[ninja_form id=1]` in theme
- Test submissions via Local **Mailpit**
- Do not delete submissions without export + user confirmation (MCP enforces workflow)

**MCP tools:** `ninjaforms-list-forms`, `ninjaforms-get-form`, `ninjaforms-get-submissions`, `ninjaforms-list-actions`, etc.

---

## Yoast SEO (`wordpress-seo`) v27.8

**Role:** SEO titles, meta descriptions, sitemaps, readability analysis.

### Story CPT SEO (local)

| Metric | Value |
|--------|--------|
| Stories indexed | 22 |
| Missing meta description | 21 of 22 |
| Avg SEO score | 62.5 |
| Avg readability score | 51.8 |
| Stories with SEO score &lt; 50 | 1 |
| Stories with readability &lt; 50 | 11 |

**Yoast dashboard statistics API** reports "no published posts" because it tracks native `post` type, not `story` CPT — use indexables table or story edit screen for scores.

### Social / Open Graph

Yoast outputs OG tags on all pages. **Theme also outputs OG tags** in `inc/template-functions.php` — results in **duplicate `og:title`** (and related tags) on home, stories, and contact pages.

| Page | Yoast `og:title` | Theme `og:title` |
|------|------------------|------------------|
| Home | "Home Page - Goshen Democrats" | "Get Involved!" (from ACF hero) |
| Story | "{title} - Goshen Democrats" | "{title}" (from ACF/content) |

**Recommendation:** Remove theme OG output OR disable Yoast social tags — pick one owner. See findings.md.

**Agent rule:** Do not add OG tags in theme PHP — Yoast owns social meta. Planned fix: remove `goshendems_opengraph_tags()` hook from `inc/template-functions.php`.

Yoast social profiles configured: Facebook group URL set; Instagram/LinkedIn empty.

**MCP tools:** `yoast-seo/get-seo-scores`, `yoast-seo/get-readability-scores` (may not reflect story CPT in dashboard)

---

## Simple Calendar (`google-calendar-events`)

**Role:** Event calendar on Calendar page.

**Theme integration:**

- `page-calendar.php` → ACF `calendar` relationship → `[calendar id="…"]` shortcode
- 1 calendar post in DB

**Agent notes:**

- Guard against empty relationship before `[0]` access
- Calendar page slug: `calendar`

---

## Classic Editor (`classic-editor`)

Disables block editor site-wide. All content editing is Classic Editor + ACF meta boxes.

---

## WP Super Cache (`wp-super-cache`)

**Local config** (`wp-content/wp-cache-config.php`):

| Setting | Value |
|---------|--------|
| Cache enabled | yes |
| Super cache enabled | yes |
| Compression | on |
| Max cache age | 1800s (30 min) |
| mod_rewrite | on |
| Don’t cache GET requests | yes (`$wp_cache_no_cache_for_get = 1`) |
| Mobile cache | off |
| Preload | off |

**Per-page-type caching flags** in config are mostly `0` — supercache still active globally via mod_rewrite mode.

**Agent notes:**

- Clear cache after theme/plugin changes if pages look stale: WP Admin → Settings → WP Super Cache → Delete Cache
- Form pages and admin should not be cached — verify contact form works after cache enabled
- `$wp_cache_not_logged_in = 2` — don’t cache for logged-in users

---

## Cloudflare (`cloudflare`)

CDN/cache plugin active (from production restore). Turnstile keys used by Ninja Forms are Cloudflare Turnstile, not necessarily the Cloudflare plugin itself.

Local environment may not mirror production Cloudflare rules.

---

## EWWW Image Optimizer (`ewww-image-optimizer`)

Compresses uploads. Regenerate thumbnails after changing custom image sizes in `inc/template-functions.php`.

---

## WP Mail SMTP (`wp-mail-smtp`)

**Local config:**

- From email: `no-reply@goshen-democrats.local`
- From name: Goshen Democrats

On Local, outbound mail routes to **Mailpit** — use to test Ninja Forms email actions.

---

## Git Updater (`git-updater`)

Pulls theme from `dugger/goshendems` on GitHub. Local theme is symlinked to repo — git pull here updates the active theme directly.

---

## All-in-One WP Migration

Used to create local restore. Do not run imports/exports without explicit user request.

---

## MCP Adapter (`mcp-adapter`)

Local dev only. See [local-environment.md](local-environment.md).

---

## DreamHost Panel Login

Host SSO plugin from production. Safe to deactivate on local if unused.

---

## Plugin hygiene notes

| Plugin | Note |
|--------|------|
| Auto Focus Keyword for SEO | Yoast helper — low theme coupling |
| Crop Thumbnails | Media library only |
| ACF Content Analysis for Yoast | Admin analysis for ACF fields |

---

## Related docs

- [architecture.md](architecture.md) — URL routing (`/story/` vs `/stories/`)
- [content-model.md](content-model.md)
- [audit/findings.md](audit/findings.md)
