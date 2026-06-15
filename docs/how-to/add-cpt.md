# How to: Add a Custom Post Type

Register a new content type for the Goshen Dems site.

---

## Overview

Custom post types are defined in **ACF Pro** and synced to `acf-json/post_type_*.json` — not via `register_post_type()` in PHP.

Reference: `acf-json/post_type_68e4df30960d2.json` (Story CPT)

---

## Steps

### 1. Create CPT in ACF

1. **ACF → Post Types → Add New**
2. Configure:
   - **Post type key:** lowercase, hyphens (e.g. `event`)
   - **Plural/singular labels**
   - **Public:** yes (for front-end URLs)
   - **Archive:** enable if needed; set slug (e.g. `events`)
   - **Supports:** title, editor, thumbnail, etc.
3. Save — creates `acf-json/post_type_*.json`

### 2. Create field group

See [add-acf-field-group.md](add-acf-field-group.md).

Location rule:

```json
{
  "param": "post_type",
  "operator": "==",
  "value": "your-post-type"
}
```

### 3. Create theme templates

| Template | When |
|----------|------|
| `archive-{post-type}.php` | Has archive |
| `single-{post-type}.php` | Single posts |
| `template-parts/content-{name}.php` | Reusable cards/rows |

Example: Story uses `archive-story.php`, `single-story.php`, `content-story-card.php`.

### 4. Register query modifications (if needed)

In `functions.php`:

```php
function goshendems_{post_type}_archive_query( $query ) {
    if ( ! is_admin() && $query->is_main_query() && $query->is_post_type_archive( 'your-type' ) ) {
        $query->set( 'posts_per_page', 9 );
    }
}
add_action( 'pre_get_posts', 'goshendems_{post_type}_archive_query' );
```

### 5. Flush rewrite rules

After adding CPT: **Settings → Permalinks → Save** (no changes needed — flushes rules).

### 6. Enqueue assets

If archive needs JS (like elected positions filter), register in `goshendems_scripts()`:

```php
if ( is_post_type_archive( 'your-type' ) ) {
    wp_enqueue_script( 'goshendems-your-type', ... );
}
```

### 7. Document

- [content-model.md](../content-model.md)
- [architecture.md](../architecture.md)
- [changelog.md](../audit/changelog.md)

---

## Elected Positions caution

`elected-positions` CPT exists in JSON but is **inactive**. Before building similar features, decide whether to activate it or create fresh — see [findings.md](../audit/findings.md).

---

## Files typically touched

| File | Change |
|------|--------|
| `acf-json/post_type_*.json` | CPT definition |
| `acf-json/group_*.json` | Field group |
| `acf-json/taxonomy_*.json` | Taxonomy (optional) |
| `archive-*.php`, `single-*.php` | Theme templates |
| `functions.php` | Queries, enqueues |
