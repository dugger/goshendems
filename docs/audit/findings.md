# Audit Findings — Goshen Democrats (Local)

Running log from the local site audit. Severity: **Critical** | **High** | **Medium** | **Low**

---

## Phase 0 findings

### [High] Elected Positions CPT inactive but theme templates exist
- **Area:** ACF / Theme
- **Location:** `acf-json/post_type_693461d475a9e.json`, `acf-json/group_693462ae5e6db.json`, `archive-elected-positions.php`
- **Issue:** CPT and field group marked `"active": false` in JSON; archive template and filter JS still present. **Confirmed:** 0 posts in DB; REST endpoint 404.
- **Impact:** Feature appears built but is non-functional; confuses editors and AI agents.
- **Recommendation:** Decide launch intent — activate CPT + field group in ACF, or remove archive template and JS until ready.
- **Effort:** S
- **Status:** Open

### [High] Elected positions filter JS not enqueued
- **Area:** Theme
- **Location:** `js/elected-positions-filter.js`, `functions.php`
- **Issue:** Filter script exists but is never registered/enqueued. Server-side `?level=` filter in PHP still works on page load.
- **Impact:** Clicking filter buttons won't update view without full page reload via URL.
- **Recommendation:** Enqueue when `is_post_type_archive('elected-positions')` (after CPT activated).
- **Effort:** S
- **Status:** Open

### [High] ACF location rules use hardcoded page IDs
- **Area:** ACF
- **Location:** All page-scoped `group_*.json` (IDs 14, 19, 76, 100)
- **Issue:** Field groups bound to numeric page IDs from this database import.
- **Impact:** Field groups may not appear after re-import if page IDs change.
- **Recommendation:** Migrate to page slug rules — see [content-model.md](../content-model.md#acf-location-rule-migration).
- **Effort:** M
- **Status:** Open

### [Medium] Contact page ignores ACF form_shortcode field
- **Area:** Theme / ACF
- **Location:** `page-contact-us.php`, `group_6904d63ed5bfd.json`
- **Issue:** Template hardcodes `[ninja_form id=1]`; ACF `form_shortcode` field unused.
- **Recommendation:** `do_shortcode( get_field('form_shortcode') )` with fallback.
- **Effort:** S
- **Status:** Open

### [Medium] Misleading `page_for_posts` setting
- **Area:** Content / Architecture
- **Location:** WP Settings — page ID 54 (`stories`) set as Posts page
- **Issue:** Site uses `story` CPT, not native posts. `/stories/` correctly renders CPT archive (`archive-story.php`), not page 54.
- **Impact:** Confusing admin setting; page 54 may be orphaned/unused on front-end.
- **Recommendation:** Clear Posts page setting or repurpose page 54; document in architecture.md.
- **Effort:** S
- **Status:** Open

### [Medium] Signup bar partial never included
- **Area:** Theme / UX
- **Location:** `template-parts/signup-bar.php`
- **Issue:** Partial exists with stub form (`alert()` on submit) but is **not** `get_template_part()`'d anywhere.
- **Impact:** Dead code; CSS for `.join-bar` unused on site.
- **Recommendation:** Include in `footer.php` or `page-home.php` when ready, or delete partial.
- **Effort:** S
- **Status:** Open

### [Low] Broken Sass build pipeline
- **Area:** Theme / DevEx
- **Location:** `package.json`
- **Issue:** References missing `sass/` directory.
- **Effort:** M
- **Status:** Open

### [Low] Stale theme metadata
- **Area:** Theme
- **Location:** `style.css`, `readme.txt`, `composer.json`
- **Issue:** Declares WP 5.4 / PHP 5.6; site runs WP 7 / PHP 8.4.
- **Effort:** S
- **Status:** Open

### [Low] Hardcoded primary navigation
- **Area:** Theme / UX
- **Location:** `header.php`
- **Issue:** `menu-1` registered but unused; nav is static HTML.
- **Effort:** M
- **Status:** Open

### [Low] Invalid HTML in story cards
- **Area:** Theme / Accessibility
- **Location:** `template-parts/content-story-card.php`
- **Issue:** `<a>` wraps `<article>`.
- **Effort:** S
- **Status:** Open

---

## Phase 1 findings — Theme code

### [High] Broken default page template
- **Area:** Theme
- **Location:** `page.php`
- **Issue:** Opens `<main>` then outputs stray `<!doctype html>` and closes via footer without content or `</main>`. Any page without a slug-matched template breaks.
- **Impact:** Page ID 54 (`stories`) and any future default pages render broken HTML.
- **Recommendation:** Implement minimal loop or redirect; remove stray doctype.
- **Effort:** S
- **Status:** Open

### [High] Unclosed `<main>` on key templates
- **Area:** Theme
- **Location:** `page-home.php`, `single-story.php`
- **Issue:** `<main id="primary">` opened but never closed before `get_footer()`.
- **Impact:** Invalid HTML document structure.
- **Recommendation:** Add `</main>` before footer on all templates.
- **Effort:** S
- **Status:** Open

### [High] Home page JavaScript error on load
- **Area:** Theme / UX
- **Location:** `page-home.php` lines 65–93
- **Issue:** Testimonial carousel script runs on every home page load, but testimonial HTML is commented out. `document.getElementById('prev')` returns null → TypeError in console.
- **Impact:** JS error on every home page visit; may block other scripts.
- **Recommendation:** Remove script or wrap in null checks; uncomment testimonials if feature is desired.
- **Effort:** S
- **Status:** Open

### [Medium] Output escaping gaps (detailed table in theme-guide.md)
- **Area:** Theme / Security
- **Location:** Multiple — see escaping audit below
- **Issue:** Raw `echo` on ACF text/URL fields without escaping.
- **Recommendation:** Apply escaping per field type across all templates.
- **Effort:** M
- **Status:** Open

| File | Field / output | Current | Should be |
|------|----------------|---------|-----------|
| `page-home.php` | hero title, description, button_text | raw echo | `esc_html()` |
| `page-home.php` | hero button_url | raw echo | `esc_url()` |
| `page-home.php` | about title, text | raw echo | `esc_html()` / `wp_kses_post()` |
| `page-about.php` | body WYSIWYG | raw echo | `wp_kses_post()` |
| `page-about.php` | title | `echo the_title()` | `the_title()` or `esc_html( get_the_title() )` |
| `page-contact-us.php` | form_intro | `the_field()` | `echo wp_kses_post( get_field(...) )` |
| `content-block-call_to_action.php` | text | raw echo | `wp_kses_post()` if HTML allowed |
| `elected-positions.php` | description | raw echo | `wp_kses_post()` |
| `content-story-card.php` | post ID in attribute | raw echo | `esc_attr()` / `absint()` |
| `content-story-card.php` | title | raw echo | `esc_html()` |
| `search.php` | search query in span | unescaped | `esc_html( get_search_query() )` |
| `header.php` | template directory URI in img src | unescaped | `esc_url( get_template_directory_uri() )` |

**Correctly handled:** `content-block-paragraph.php` (WYSIWYG + phpcs ignore), `content-block-video_embed.php` (oEmbed), OG meta output in `template-functions.php`, `archive-elected-positions.php` GET param sanitization.

### [Medium] Calendar page missing empty guard
- **Area:** Theme
- **Location:** `page-calendar.php` line 22
- **Issue:** `get_field('calendar')[0]` without checking field exists or array is non-empty.
- **Impact:** PHP warnings/errors if calendar not configured.
- **Recommendation:** Guard before accessing index 0.
- **Effort:** S
- **Status:** Open

### [Medium] Home page missing ACF null guards
- **Area:** Theme
- **Location:** `page-home.php`
- **Issue:** `$hero['image']`, `$about['image']` accessed without checking `$hero` / `$about` arrays exist.
- **Impact:** PHP warnings if fields empty.
- **Recommendation:** Wrap sections in `is_array()` / `! empty()` checks.
- **Effort:** S
- **Status:** Open

### [Medium] OpenGraph URL built from `$_SERVER`
- **Area:** Theme / Security
- **Location:** `inc/template-functions.php` line 107
- **Issue:** `HTTP_HOST` + `REQUEST_URI` concatenated for `og:url`.
- **Impact:** Host header injection in misconfigured environments; duplicates Yoast canonical work.
- **Recommendation:** Use `get_permalink()` / `home_url( add_query_arg( array() ) )` or remove if Yoast handles.
- **Effort:** S
- **Status:** Open

### [Medium] Theme + Yoast OG overlap
- **Area:** Theme / SEO
- **Location:** `inc/template-functions.php` vs Yoast SEO plugin
- **Issue:** Both may output OpenGraph tags on same pages.
- **Impact:** Duplicate or conflicting social meta.
- **Recommendation:** Phase 3 — pick one owner; disable the other for OG tags.
- **Effort:** S
- **Status:** Open

### [Low] Duplicate `get_field()` call
- **Area:** Theme
- **Location:** `template-parts/elected-positions.php` line 20
- **Issue:** `get_field('requirements')` called twice in loop.
- **Recommendation:** Reuse `$requirements` variable.
- **Effort:** S
- **Status:** Open

### [Low] Donate link missing `rel="noopener noreferrer"`
- **Area:** Theme / Security
- **Location:** `header.php` line 40
- **Issue:** `target="_blank"` without rel attribute.
- **Effort:** S
- **Status:** Open

### [Low] PHPCS not runnable
- **Area:** DevEx
- **Location:** `phpcs.xml.dist`, `composer.json`
- **Issue:** WordPress PHPCS sniffs not installed; `composer install` needed.
- **Recommendation:** Run `composer install` in theme repo for automated linting.
- **Effort:** S
- **Status:** Open

### [Low] Orphan _s templates
- **Area:** Theme
- **Location:** `search.php` calls `get_sidebar()`; sidebar unused site-wide
- **Issue:** Underscores boilerplate not cleaned up.
- **Effort:** S
- **Status:** Open

---

## Phase 2 findings — ACF & content model

### [High] Elected Positions feature is dormant end-to-end
- **Area:** ACF / Content
- **Issue:** CPT inactive, field group inactive, 0 posts, 0 level terms in use, REST 404, JS not enqueued.
- **Recommendation:** Treat as **not a live feature** in agent docs until activated; see [content-model.md](../content-model.md).
- **Effort:** S (documentation) / M (activation)
- **Status:** Open

### [Medium] ACF location rule migration needed
- **Area:** ACF
- **Issue:** Four page field groups use numeric IDs.
- **Recommendation:** Replace with slug-based rules (documented in content-model.md).
- **Effort:** M
- **Status:** Open

### [Medium] Story excerpt logic duplicated
- **Area:** Theme
- **Location:** `content-story-card.php`, `goshendems_get_story_description()` in `template-functions.php`
- **Issue:** Two implementations extract first paragraph from story body flexible content.
- **Recommendation:** Consolidate into shared helper function.
- **Effort:** S
- **Status:** Open

### [Low] Page templates hide classic editor content
- **Area:** ACF
- **Location:** ACF field groups with `hide_on_screen: the_content`
- **Issue:** All major pages use ACF fields only; classic editor content ignored.
- **Impact:** Expected for this site — document so agents don't edit `post_content` expecting front-end changes.
- **Status:** Documented

---

## Phase 3 findings — Plugins

### [High] Duplicate Open Graph tags (Yoast + theme)
- **Area:** Plugin / Theme / SEO
- **Location:** `inc/template-functions.php` (`goshendems_opengraph_tags`) + Yoast SEO
- **Issue:** Confirmed on home, story, and contact pages — **two `og:title` tags** per page (Yoast + theme). Theme uses ACF hero on home; Yoast uses page title template.
- **Impact:** Social platforms may pick unpredictable preview; invalid/conflicting meta.
- **Recommendation:** Remove theme OG hook OR disable Yoast Open Graph — single owner. Theme OG is redundant if Yoast active.
- **Effort:** S
- **Status:** Open

### [Medium] 21 of 22 stories missing Yoast meta descriptions
- **Area:** Plugin / Content / SEO
- **Location:** Yoast indexables for `story` CPT
- **Issue:** Avg SEO score 62.5, avg readability 51.8; 11 stories with readability &lt; 50; only 1 with SEO &lt; 50.
- **Impact:** Weak search snippets; readability gaps on majority of stories.
- **Recommendation:** Batch-add meta descriptions; review low-readability stories for shorter paragraphs/headings.
- **Effort:** M
- **Status:** Open

### [Medium] Yoast dashboard ignores story CPT
- **Area:** Plugin / SEO
- **Location:** Yoast statistics REST endpoint
- **Issue:** Dashboard reports "no published posts" — tracks native `post`, not `story`.
- **Impact:** Admin SEO overview misleading; MCP Yoast score tools may not reflect stories.
- **Recommendation:** Configure Yoast for custom post types or use per-story editor scores / indexables query.
- **Effort:** S
- **Status:** Open

### [Low] Ninja Forms name field missing GDPR flag
- **Area:** Plugin
- **Location:** Contact form field `name`
- **Issue:** Email and phone have `personally_identifiable: true`; name field does not.
- **Recommendation:** Set flag for consistency if Ninja GDPR export is used.
- **Effort:** S
- **Status:** Open

### [Low] Ninja Forms confirmation action has stale default subject
- **Area:** Plugin
- **Location:** Email Confirmation action (ID 2) internal `subject` field
- **Issue:** Contains placeholder "This is an email action." — actual `email_subject` is correct and used.
- **Impact:** None if unused field; cosmetic in admin.
- **Effort:** S
- **Status:** Open

### Positive — Ninja Forms well configured
- Cloudflare Turnstile spam protection active on form
- Save + admin notification + user confirmation emails active
- 42 submissions stored locally
- Email/phone marked personally identifiable

### Positive — WP Super Cache active with sensible defaults
- Super cache on, compression on, GET requests not cached, logged-in users not cached

---

## Phase 4 findings — Security (local)

### [Medium] XML-RPC enabled
- **Area:** Security
- **Location:** `/xmlrpc.php`
- **Issue:** Returns HTTP 200; brute-force and pingback attack surface.
- **Recommendation:** Disable if unused (plugin or server rule).
- **Effort:** S
- **Status:** Open

### [Medium] Author enumeration exposes usernames
- **Area:** Security
- **Location:** `/?author=1` → 301 to `/author/alex/`
- **Issue:** Reveals login slug `alex` to unauthenticated visitors.
- **Recommendation:** Disable author archives or redirect to home; use non-obvious login names.
- **Effort:** S
- **Status:** Open

### [Low] Six WordPress user accounts
- **Area:** Security
- **Location:** WP Users
- **Issue:** alex, jon, paul, michael, marilyn, sara — review role/capability for each; remove unused.
- **Recommendation:** Audit accounts; enforce 2FA on production when available.
- **Effort:** S
- **Status:** Open

### [Low] Comments default to open
- **Area:** Security / Spam
- **Location:** WP Settings
- **Issue:** `default_comment_status` is `open`; stories unlikely to use comments but setting is permissive.
- **Recommendation:** Set defaults to closed if comments unused site-wide.
- **Effort:** S
- **Status:** Open

### Positive — Local security hygiene
- `.cursor/mcp.json` gitignored (Application Password not in repo)
- No API keys/secrets in theme repo
- Turnstile secrets stored in WP options only
- Contact form has Turnstile spam protection
- MCP Adapter is local-dev tooling

---

## Phase 5 findings — Usability & UX

*Tested via browser on local site (desktop + 375px mobile). Full journey notes: [user-journeys.md](../user-journeys.md).*

### [High] Stale "building this site" copy
- **Area:** Content / UX
- **Location:** Home page ACF `about_section`, About page body
- **Issue:** Text says "We are currently building this site and hope you check back…" but site has 22 stories, calendar, contact form, events.
- **Impact:** Undermines credibility; confuses visitors about site readiness.
- **Recommendation:** Update ACF content on home + about to reflect current offerings (stories, events, volunteer/contact CTAs).
- **Effort:** S
- **Status:** Open

### [High] Story card accessible names are excessively long
- **Area:** UX / Accessibility
- **Location:** `template-parts/content-story-card.php`
- **Issue:** Entire card is one link; screen reader announces title + date + full excerpt as link name (200+ chars).
- **Impact:** Poor screen reader UX; violates link purpose best practices.
- **Recommendation:** Restructure card HTML; use `aria-labelledby` on a short title only, or link only the title.
- **Effort:** M
- **Status:** Open

### [High] Home page JavaScript error (confirmed in browser)
- **Area:** UX
- **Location:** `page-home.php` testimonial script
- **Issue:** `document.getElementById('prev')` is null — TypeError on every home page load.
- **Impact:** Console error; potential script pipeline breakage.
- **Recommendation:** Remove orphaned script or uncomment testimonial HTML.
- **Effort:** S
- **Status:** Open

### [Medium] 404 page uses uncustomized _s template
- **Area:** UX
- **Location:** `404.php`
- **Issue:** Generic WP message + empty Recent Posts, Categories, Archives widgets (no native posts).
- **Impact:** Dead-end experience off-brand for political org site.
- **Recommendation:** Custom 404 with links to Home, Stories, Calendar, Contact; remove useless widgets.
- **Effort:** S
- **Status:** Open

### [Medium] Search results page shows empty _s sidebar widgets
- **Area:** UX
- **Location:** `search.php`, `content-search.php`
- **Issue:** Recent Posts, Comments, Archives, Categories widgets all empty; sidebar called via `get_sidebar()`.
- **Impact:** Cluttered, unhelpful search experience despite relevant story results.
- **Recommendation:** Remove sidebar; style search results to match story cards; add search to nav optionally.
- **Effort:** M
- **Status:** Open

### [Medium] CTA blocks display raw URLs as link text
- **Area:** UX / Content
- **Location:** Story flexible content `call_to_action` block (e.g. Trivia Party story)
- **Issue:** Google Forms URL shown as full link text instead of button label like "Register" or "Sign up".
- **Impact:** Ugly, hard to read, poor accessibility.
- **Recommendation:** Set `button_label` in ACF; audit existing stories with CTA blocks.
- **Effort:** S
- **Status:** Open

### [Medium] No search in primary navigation
- **Area:** UX
- **Location:** `header.php`
- **Issue:** Search only available on 404/search results pages, not in main nav.
- **Impact:** Users can't discover search; search works when URL is known.
- **Recommendation:** Add search icon/link or header search form.
- **Effort:** S
- **Status:** Open

### [Medium] `/elected-positions/` returns 404
- **Area:** UX
- **Location:** Would-be archive URL
- **Issue:** Confirmed in browser — 404 page, not archive template.
- **Impact:** Any future links to elected positions will fail until CPT activated.
- **Status:** Known — documented in content-model.md

### [Low] Nav label "Events" vs URL `/calendar`
- **Area:** UX
- **Location:** `header.php`
- **Issue:** Link text "Events" points to `/calendar` — minor mental model mismatch.
- **Recommendation:** Align label and slug, or add redirect note in docs.
- **Effort:** S
- **Status:** Open

### Positive — UX patterns that work
- Mobile hamburger menu toggles with `aria-expanded`
- Skip to content link
- Home → Contact hero CTA path clear
- Stories archive + pagination functional
- Calendar page rich with real events (Simple Calendar)
- Contact form fields labeled and usable
- Search finds relevant story content
- Footer includes org email

---

## Phase 6 — Doc assembly

**Complete.** Deliverables:

- [summary.md](summary.md) — executive summary + prioritized fix backlog (27 actionable items)
- Updated [AGENTS.md](../../AGENTS.md) — audit complete, backlog link
- Full documentation tree in `docs/` for AI agent reference

---

## Summary counts

| Severity | Open |
|----------|------|
| Critical | 0 |
| High | 9 |
| Medium | 24 |
| Low | 13 |

*Last updated: Phase 6 — audit complete*
