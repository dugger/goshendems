# How to: Add an ACF Field Group

Add or modify Advanced Custom Fields in the Goshen Dems theme.

---

## Overview

ACF Pro syncs field definitions to `acf-json/` in the theme. CPTs and taxonomies also live here as JSON — not in PHP.

---

## Steps

### 1. Create in WP Admin (recommended)

1. **Custom Fields → Add New**
2. Add fields with clear `name` keys (snake_case)
3. Set **Location rules** — prefer:
   - Page template
   - Page slug
   - Post type
   - **Not** page ID unless documented in `content-model.md`
4. Save — JSON file appears in `acf-json/`

### 2. Or edit JSON directly

1. Edit `acf-json/group_*.json` in this repo
2. WP Admin → **Custom Fields → Sync** (when "Sync available" appears)
3. Confirm field group in admin

### 3. Use fields in templates

```php
$value = get_field( 'field_name' );
if ( $value ) {
    echo esc_html( $value );
}
```

For groups:

```php
$section = get_field( 'section' );
if ( is_array( $section ) && ! empty( $section['title'] ) ) {
    echo esc_html( $section['title'] );
}
```

### 4. Commit JSON

Always commit changes to `acf-json/*.json` with the template changes that use them.

### 5. Update docs

- [content-model.md](../content-model.md)
- [changelog.md](../audit/changelog.md)

---

## Naming conventions

| Item | Convention | Example |
|------|------------|---------|
| Field name | snake_case | `hero_image`, `form_intro` |
| Group name | Title Case in admin | "Contact Page" |
| Flexible layout | snake_case | `call_to_action` |
| JSON file | Auto-generated key | `group_68caf1803c23a.json` |

---

## WYSIWYG toolbar

Story/page WYSIWYG fields can use toolbar **"Very Simple"** (registered in `functions.php`).

---

## Related

- [add-cpt.md](add-cpt.md) — post types via ACF
- [add-story-block.md](add-story-block.md) — flexible content layouts
- [content-model.md](../content-model.md) — existing field reference
