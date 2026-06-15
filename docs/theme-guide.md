# Theme Guide — Goshen Dems

Conventions for editing the `goshendems` WordPress theme. Based on Underscores (_s) v1.2.0.

---

## Template hierarchy (in use)

| Condition | Template |
|-----------|----------|
| Front page (slug `home`) | `page-home.php` |
| Page slug `about` | `page-about.php` |
| Page slug `calendar` | `page-calendar.php` |
| Page slug `contact-us` | `page-contact-us.php` |
| Other pages | `page.php` *(minimal/broken — avoid for new pages)* |
| Story archive | `archive-story.php` |
| Single story | `single-story.php` |
| Elected positions archive | `archive-elected-positions.php` |
| Search | `search.php` |
| 404 | `404.php` |

---

## Key files

| File | Purpose |
|------|---------|
| `functions.php` | Theme setup, enqueues, story archive query (9/page), ACF WYSIWYG toolbar |
| `header.php` | `<head>`, Typekit, hardcoded nav, skip link |
| `footer.php` | Site footer |
| `inc/template-functions.php` | OpenGraph tags, custom image sizes, story description helper |
| `inc/template-tags.php` | Post meta template tags (_s) |
| `style.css` | All theme CSS (~1500 lines) |

---

## Template parts

```
template-parts/
├── content-story-card.php          # Story card on archive/home
├── content-featured-stories.php    # Home featured stories grid
├── content-none.php
├── content-search.php
├── elected-positions.php           # Elected position row partial
└── content-block-*.php             # Story flexible content layouts
```

---

## Enqueued assets

From `goshendems_scripts()` in `functions.php`:

| Handle | File | When |
|--------|------|------|
| `goshendems-style` | `style.css` | Always |
| `goshendems-navigation` | `js/navigation.js` | Always (mobile menu toggle) |
| `comment-reply` | WP core | Singular + comments open |

**Not enqueued:** `js/elected-positions-filter.js` (needed by elected positions archive).

**External (not enqueued):** Adobe Typekit in `header.php`.

---

## Custom image sizes

Registered in `inc/template-functions.php`:

| Size | Dimensions | Usage |
|------|------------|-------|
| `hero_image` | — | Story/page heroes |
| `hero_short` | — | Shorter hero crops |
| `highlight` | — | Featured/highlight cards |
| `opengraph` | 1200×630 | OG image generation |

---

## OpenGraph (theme-level SEO)

`inc/template-functions.php` outputs custom `<meta property="og:*">` tags:

- Home: hero image from ACF
- Stories: hero or first paragraph excerpt via `goshendems_get_story_description()`
- Uses `esc_attr()` / `esc_url()` on output
- OG URL built from `$_SERVER` — prefer `get_permalink()` for new code

Yoast SEO also active — coordinate to avoid duplicate/conflicting meta (audit pending).

---

## PHP conventions

### Escaping (follow strictly)

| Context | Function |
|---------|----------|
| Plain text | `esc_html()` |
| Attributes | `esc_attr()` |
| URLs | `esc_url()` |
| WYSIWYG HTML | `wp_kses_post()` |
| oEmbed | Output as-is with phpcs ignore |

### ACF fields

```php
$hero = get_field( 'hero' );
if ( is_array( $hero ) && ! empty( $hero['title'] ) ) {
    echo esc_html( $hero['title'] );
}
```

Always guard flexible content / group fields before access.

### Flexible content loop

```php
$body = get_field( 'body' );
if ( is_array( $body ) && ! empty( $body ) ) {
    foreach ( $body as $row ) {
        set_query_var( 'content_block_row', $row );
        get_template_part( 'template-parts/content-block', $row['acf_fc_layout'] );
    }
}
```

Partials read `$row` via `get_query_var( 'content_block_row' )`.

### Shortcodes

```php
echo do_shortcode( get_field( 'form_shortcode' ) ); // preferred
// Not: hardcoded [ninja_form id=1]
```

---

## CSS

- Single file: `style.css` (no active Sass pipeline — `package.json` references missing `sass/`)
- Edit CSS directly or restore Sass workflow before large style refactors
- RTL variant: `style-rtl.css` via `wp_style_add_data`

---

## Adding new functionality

| Change | Guide |
|--------|-------|
| Story content block | [how-to/add-story-block.md](how-to/add-story-block.md) |
| New page template | [how-to/add-page-template.md](how-to/add-page-template.md) |
| ACF field group | [how-to/add-acf-field-group.md](how-to/add-acf-field-group.md) |
| Custom post type | [how-to/add-cpt.md](how-to/add-cpt.md) |

After any structural change, update:

- `docs/content-model.md`
- `docs/audit/changelog.md`

---

## PHPCS

Config: `phpcs.xml.dist`. Run via Composer if installed:

```bash
composer run phpcs
```

---

## Known tech debt (theme)

See [audit/findings.md](audit/findings.md). Phase 1 summary:

- `page.php` broken; `<main>` unclosed on home and single-story templates
- Home page JS error from orphaned testimonial script
- Escaping gaps — see escaping table in [findings.md](audit/findings.md)
- Calendar page missing empty guard

---

## Template checklist (for new/edited templates)

- [ ] `get_header()` / `get_footer()` bookend file
- [ ] `<main id="primary">` opened **and closed**
- [ ] No stray `<!doctype html>` inside templates
- [ ] ACF fields guarded before access
- [ ] Output escaped per field type
- [ ] Scripts enqueued in `functions.php`, not inline (unless justified)
- [ ] Update `docs/content-model.md` if fields change
