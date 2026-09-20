# How to: Add a Page Template

Create a new custom page template for the Goshen Dems theme.

---

## Overview

WordPress auto-loads `page-{slug}.php` when a page slug matches the filename. For explicit templates, use `page-{name}.php` or the Template Name header.

Existing examples: `page-home.php`, `page-about.php`, `page-calendar.php`, `page-contact-us.php`.

Pages that do **not** need a custom layout use default `page.php` and the **Page fields** ACF group (same flexible content blocks as stories). Only add a named template when the page needs a different structure (home sections, calendar, contact form, and so on).

---

## Steps

### 1. Create template file

Create `page-{slug}.php` at theme root:

```php
<?php
/**
 * Template for {Page Name} page
 *
 * @package Goshen_Dems
 */

get_header();
?>

<main id="primary" class="site-main">
    <?php
    while ( have_posts() ) :
        the_post();
        // Template content + ACF fields
    endwhile;
    ?>
</main>

<?php
get_footer();
```

Follow patterns from `page-about.php` (simple) or `page-home.php` (sections).

### 2. Create ACF field group (if needed)

See [add-acf-field-group.md](add-acf-field-group.md).

**Use page slug or template location rule — not page ID:**

```json
{
  "param": "page_template",
  "operator": "==",
  "value": "page-your-template.php"
}
```

Or:

```json
{
  "param": "page",
  "operator": "==",
  "value": "your-page-slug"
}
```

### 3. Create WordPress page

1. Pages → Add New
2. Set slug to match template (e.g. slug `events` → `page-events.php`)
3. Publish
4. Fill ACF fields
5. For a section of related pages: set **Page Attributes → Parent** on the child pages (one level). Add the parent to the Primary menu. **Order** controls the header section links.

### 4. Add navigation link (if needed)

Nav is hardcoded in `header.php` — add a `<li>` to `#primary-menu` manually until nav is migrated to WP Menus.

### 5. Verify & document

- Test URL on local site
- Update [content-model.md](../content-model.md) page table
- Update [architecture.md](../architecture.md) URL map

---

## Files typically touched

| File | Change |
|------|--------|
| `page-{slug}.php` | New template |
| `acf-json/group_*.json` | New field group (optional) |
| `header.php` | Nav link (current pattern) |
| `style.css` | Page-specific styles |

---

## Avoid

- Creating a named template when `page.php` + Page fields already fit (same blocks as stories)
- Duplicating `<!doctype html>` inside templates (Underscores leftover in some files)
- Hardcoding page IDs in ACF location rules
- Adding Page fields to Home/About/Calendar/Contact — those slugs are excluded because they still show “Default template” in admin while rendering `page-{slug}.php`
