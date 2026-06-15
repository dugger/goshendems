# Content Model — Goshen Democrats

ACF-driven content structure. Page IDs reflect **local restore** — treat as fragile; prefer slug/template location rules for new work.

---

## Pages (local)

| ID | Slug | Title | Template file | ACF group |
|----|------|-------|---------------|-----------|
| 14 | `home` | Home Page | `page-home.php` | Home Page (`group_68caf1803c23a`) |
| 19 | `calendar` | Calendar | `page-calendar.php` | Calendar Page (`group_68dd3f01ce692`) |
| 54 | `stories` | Stories | *(default `page.php`)* | — |
| 76 | `about` | About Goshen City Democratic Party | `page-about.php` | About Page (`group_68e54daba70f6`) |
| 100 | `contact-us` | Contact Us | `page-contact-us.php` | Contact Page (`group_6904d63ed5bfd`) |

WordPress uses `page-{slug}.php` automatically when the slug matches (e.g. `page-home.php` for slug `home`).

Front page: **ID 14**. Posts page setting: **ID 54** (`stories`) — **misleading**; site uses `story` CPT. `/stories/` renders the CPT archive, not page 54.

---

## ACF location rules

Page field groups use **slug-based or front-page rules** (not page IDs):

| Field group | Location rule |
|-------------|----------------|
| Home Page | `page_type` == `front_page` |
| Calendar Page | `page_slug` == `calendar` |
| About Page | `page_slug` == `about` |
| Contact Page | `page_slug` == `contact-us` |

Custom location rule: `inc/acf-page-slug-location.php` registers the `page_slug` matcher with ACF.

Story and elected-position groups use `post_type` rules (good pattern).

After changing rules in JSON, **Custom Fields → Sync** in admin if prompted.

---

## ACF location rule migration (complete)

Previously used numeric page IDs (fragile on import). Migrated June 2026 — see table above.

## Custom post types

### Story (`story`) — **active**

- **JSON:** `acf-json/post_type_68e4df30960d2.json`
- **Archive slug:** `stories` → `/stories/`
- **Archive template:** `archive-story.php` (9 posts per page via `pre_get_posts`)
- **Single template:** `single-story.php`
- **Count (local):** 22+ published
- **Field group:** Story fields (`group_68e4df466f654`)

#### Story fields

| Field | Type | Template usage |
|-------|------|----------------|
| `hero_image` | Image (returns ID) | `single-story.php` hero |
| `body` | Flexible Content | Looped in `single-story.php` → `content-block-{layout}` |

#### Flexible content layouts (`body`)

| Layout name | Partial | Subfields |
|-------------|---------|-----------|
| `paragraph` | `content-block-paragraph.php` | `text` (WYSIWYG) |
| `full_width_image` | `content-block-full_width_image.php` | `image`, `caption` |
| `call_to_action` | `content-block-call_to_action.php` | `heading`, `text`, `button_label`, `button_url` |
| `pull_quote` | `content-block-pull_quote.php` | `quote`, `attribution` |
| `video_embed` | `content-block-video_embed.php` | `url` (oEmbed) |

Loop pattern in `single-story.php`:

```php
foreach ( $body as $row ) {
    set_query_var( 'content_block_row', $row );
    get_template_part( 'template-parts/content-block', $row['acf_fc_layout'] );
}
```

---

### Resource (`resource`) — **active**

- **JSON:** `acf-json/post_type_674a1b10resource.json`
- **Archive slug:** `resources` → `/resources/`
- **Archive template:** `archive-resource.php` (all posts, no pagination)
- **Single URLs:** Redirect to `/resources/#resource-{slug}` (no single template)
- **Field group:** Resource fields (`group_674a1b30resource`)
- **Settings:** ACF Options sub-page under Resources → Settings (`group_674a1b40resopts`)
- **Taxonomy:** `resource-category` (`acf-json/taxonomy_674a1b20rescat.json`) — client-side filter on archive

#### Resource fields

| Field | Type | Notes |
|-------|------|-------|
| `resource_type` | Button group | `file` or `external_link` |
| `file` | File | When type is file |
| `external_url` | URL | When type is external link |

#### Resources settings (options page)

| Field | Type | Notes |
|-------|------|-------|
| `resources_intro` | WYSIWYG | Intro copy on archive |
| `resources_order` | Relationship → `resource` | Manual display order |

Cards link directly to the file or URL (new tab). Category badges shown; empty categories hidden from filter.

**After deploy:** Custom Fields → Sync, then **Settings → Permalinks → Save**.

---

### Candidate (`candidate`) — **active**

- **JSON:** `acf-json/post_type_674a1c10candidate.json`
- **Archive slug:** `candidates` → `/candidates/`
- **Archive template:** `archive-candidate.php` (9 per page, paginated)
- **Single template:** `single-candidate.php`
- **Single URLs:** `/candidate/{slug}/`
- **Field group:** Candidate fields (`group_674a1c20candidate`)
- **Settings:** ACF Options sub-page under Candidates → Settings (`group_674a1c30candopts`)
- **Name:** WordPress post title

#### Candidate fields

| Field | Type | Notes |
|-------|------|-------|
| `picture` | Image (returns ID) | Headshot or portrait |
| `race` | Text | Office or race name |
| `bio` | Textarea | Candidate biography |
| `phone` | Text | Contact phone number |
| `links` | Repeater | External links (see below); drag rows to reorder |

#### Link repeater (`links`)

| Sub-field | Type | Notes |
|-----------|------|-------|
| `title` | Text | Link label |
| `url` | URL | Destination URL |
| `thumbnail` | Image | Auto-filled from Open Graph on save |
| `og_source_url` | Text | Internal; hidden in admin; tracks URL used for thumbnail |

Rows are drag-sortable in the admin. Display order matches row order on the single candidate page.

#### Candidates settings (options page)

| Field | Type | Notes |
|-------|------|-------|
| `candidates_intro` | WYSIWYG | Intro copy on archive |
| `candidates_order` | Relationship → `candidate` | Manual archive and prev/next order |

**After deploy:** Custom Fields → Sync, then **Settings → Permalinks → Save**.

---

### Elected Positions (`elected-positions`) — **inactive / dormant**

- **JSON:** `acf-json/post_type_693461d475a9e.json` (`"active": false`)
- **Archive template exists:** `archive-elected-positions.php`
- **Field group:** Elected Position Fields (`group_693462ae5e6db`) — also **inactive**
- **Taxonomy:** `level` (`acf-json/taxonomy_69347cdeefd62.json`) — **active**
- **Local DB:** 0 published posts; REST API 404; feature not live
- **Agent note:** Do not build against this CPT until activated in ACF admin

#### Elected position fields (when active)

| Field | Type |
|-------|------|
| `level` | Taxonomy (`level`) |
| `description` | WYSIWYG |
| `requirements` | WYSIWYG |
| `salary` | Text |
| `schedule` | Text |
| `term_length` | Text |

Archive supports `?level={slug}` filter; JS filter in `js/elected-positions-filter.js` (**not enqueued** — see findings).

---

## ACF field groups by page

### Home Page (`group_68caf1803c23a`)

Location: **front page** (`page_type`)

| Field | Type | Notes |
|-------|------|-------|
| `hero` | Group | `image`, `title`, `description`, `button_url`, `button_text` |
| `about_section` | Group | About blurb on home |
| `featured_stories` | Relationship | Up to 3 stories; backfills in `content-featured-stories.php` |

### About Page (`group_68e54daba70f6`)

Location: **page slug** `about`

| Field | Type |
|-------|------|
| `hero_image` | Image |
| `body` | WYSIWYG |

### Calendar Page (`group_68dd3f01ce692`)

Location: **page slug** `calendar`

| Field | Type |
|-------|------|
| `calendar` | Relationship → calendar CPT (Simple Calendar) |

### Contact Page (`group_6904d63ed5bfd`)

Location: **page slug** `contact-us`

| Field | Type | Template usage |
|-------|------|----------------|
| `form_intro` | Text/WYSIWYG | Rendered in `page-contact-us.php` |
| `form_shortcode` | Text | **Defined but unused** — template hardcodes `[ninja_form id=1]` |

---

## ACF JSON sync

All definitions live in `acf-json/` at theme root. ACF Pro auto-loads from this directory.

**Workflow:**

1. Edit fields in WP admin → saves to JSON on save.
2. Or edit JSON in repo → **Custom Fields → Sync** in admin.

Files:

```
acf-json/
├── group_68caf1803c23a.json    # Home Page
├── group_68e54daba70f6.json    # About Page
├── group_68dd3f01ce692.json    # Calendar Page
├── group_6904d63ed5bfd.json    # Contact Page
├── group_68e4df466f654.json    # Story fields
├── group_674a1b30resource.json # Resource fields
├── group_674a1b40resopts.json  # Resources settings (options)
├── group_674a1c20candidate.json # Candidate fields
├── group_674a1c30candopts.json # Candidates settings (options)
├── group_693462ae5e6db.json    # Elected Position Fields (inactive)
├── post_type_68e4df30960d2.json
├── post_type_674a1b10resource.json
├── post_type_674a1c10candidate.json
├── post_type_693461d475a9e.json
├── taxonomy_674a1b20rescat.json
└── taxonomy_69347cdeefd62.json
```

---

## Navigation (WordPress Menus)

Primary nav uses **Appearance → Menus**, location **Primary** (`menu-1`).

Header social icons use **Appearance → Menus**, location **Social** (`menu-2`).

| Label | Target |
|-------|--------|
| About | Page: `/about/` |
| Events | Page: `/calendar/` |
| Stories | Custom link: `/stories/` (story archive) |
| Contact | Page: `/contact-us/` |
| Donate | Custom link: ActBlue (opens in new tab) |

Resources (`/resources/`) and Candidates (`/candidates/`) can be added manually in **Appearance → Menus** when ready. The theme no longer auto-adds or restores them.

**Theme behavior:**
- Rendered via `goshendems_primary_nav_menu()` in `header.php` (`inc/nav-menus.php`)
- Mobile drawer prepends a light logo item (theme-controlled, not in the menu editor)
- Desktop brand logo remains in the header bar (theme-controlled)
- On first admin visit after theme update, a default **Primary** menu is created if none exists

**Edit nav:** WP Admin → Appearance → Menus → assign to **Primary**. Placeholder menus (e.g. "Menu 1") are auto-replaced on first admin visit, or use the theme fallback until then.

**Social menu defaults:** Facebook group + Instagram custom links. Icons are chosen from the URL (or Navigation Label / CSS class). Links open in a new tab. The theme seeds a **Social** menu only when no Social location is assigned — removing links in the menu editor sticks (no auto-restore).

**Edit social links:** Appearance → Menus → **Social** menu → assign to **Social** location.

**WP-CLI (Local Site Shell):**

```bash
wp menu list
wp menu item list primary
```

---

## WYSIWYG toolbar

Custom ACF toolbar **"Very Simple"** registered in `functions.php`:

`formatselect`, `bold`, `italic`, `link`, `alignleft`, `aligncenter`, `alignright`

---

## Related docs

- [how-to/add-story-block.md](how-to/add-story-block.md)
- [how-to/add-acf-field-group.md](how-to/add-acf-field-group.md)
- [how-to/add-cpt.md](how-to/add-cpt.md)
- [theme-guide.md](theme-guide.md)
