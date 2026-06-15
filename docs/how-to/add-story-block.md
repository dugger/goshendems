# How to: Add a Story Content Block

Add a new layout to the Story flexible content field (`body`) on the `story` post type.

---

## Overview

Story content blocks use ACF Flexible Content → theme template partials. Each layout name must match a `template-parts/content-block-{layout}.php` file.

Existing layouts: `paragraph`, `full_width_image`, `call_to_action`, `pull_quote`, `video_embed`

---

## Steps

### 1. Add layout in ACF

1. WP Admin → **Custom Fields** → **Story fields** (or edit `acf-json/group_68e4df466f654.json`)
2. In the `body` flexible content field, add a new layout
3. Name the layout with lowercase + underscores (e.g. `image_gallery`)
4. Add subfields for the block
5. Save — ACF syncs to `acf-json/group_68e4df466f654.json`

### 2. Create template partial

Create `template-parts/content-block-{layout}.php`:

```php
<?php
$row = get_query_var( 'content_block_row' );
if ( ! is_array( $row ) ) {
    return;
}
?>
<section class="content-block content-block--your-layout">
    <!-- Escape output appropriately -->
</section>
```

Reference existing blocks for patterns:

- Text: `content-block-paragraph.php` (`wp_kses_post`)
- Media: `content-block-full_width_image.php`
- CTA: `content-block-call_to_action.php` (`esc_html`, `esc_url`)

### 3. Add CSS

Add styles to `style.css` under the content blocks section. Use BEM-style class: `.content-block--your-layout`.

### 4. Verify

1. Edit a story in WP admin
2. Add the new block, fill fields, publish
3. View single story on http://goshen-democrats.local/stories/{slug}/
4. Check mobile layout

### 5. Update docs

- Add layout to [content-model.md](../content-model.md)
- Note in [changelog.md](../audit/changelog.md)

---

## Files typically touched

| File | Change |
|------|--------|
| `acf-json/group_68e4df466f654.json` | New layout + subfields |
| `template-parts/content-block-{layout}.php` | New partial |
| `style.css` | Block styles |

No change to `single-story.php` needed — the flexible content loop auto-loads partials by layout name.

---

## Do not

- Use spaces or hyphens in ACF layout names (use underscores to match `get_template_part` slug)
- Echo ACF text fields without escaping
- Forget to sync ACF JSON before committing
