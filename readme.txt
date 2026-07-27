=== Goshen Dems ===

Contributors: dugger
Tags: custom-background, custom-logo, custom-menu, featured-images, translation-ready, block-styles, wide-blocks
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 8.0
Stable tag: 1.3.3
License: GNU General Public License v2 or later
License URI: LICENSE

Custom WordPress theme for the Goshen City Democratic Party.

== Description ==

ACF Pro-driven theme for goshendems.org with custom page templates, a Story custom post type with flexible content blocks, Simple Calendar integration, and Ninja Forms contact flows.

Built on Underscores (_s) and maintained in GitHub at https://github.com/dugger/goshendems

== Installation ==

1. Clone or copy the theme into `wp-content/themes/goshendems`.
2. Activate the theme in Appearance → Themes.
3. Sync ACF field groups from `acf-json/` if prompted (Custom Fields → Sync).

== Frequently Asked Questions ==

= Does this theme require ACF Pro? =

Yes. Page fields, the Story CPT, and flexible content blocks depend on ACF Pro with JSON sync enabled.

= Which plugins does this theme integrate with? =

Advanced Custom Fields Pro, Ninja Forms, Yoast SEO, Simple Calendar (Google Calendar Events), and WP Super Cache. See `docs/plugins.md` in the theme repository.

== Changelog ==

= 1.3.3 - July 2026 =
* Candidates: show phone, email, and district on profiles and archive cards
* Candidates archive: 12 per page and restyled pagination

= 1.3.2 - June 2026 =
* Social links editable via Appearance → Menus (Social location)
* Home hero: capped title/button sizes; description hides when CTA overflows

= 1.3.1 - June 2026 =
* Stop auto-restoring Resources and Candidates in the Primary menu when removed in WP Admin

= 1.3.0 - June 2026 =
* Resources CPT with archive, category filtering, and options page ordering
* Candidates CPT with profile pages, Open Graph link thumbnails, and manual ordering
* Header redesign: two-row layout, social links, 1140px content width, nav offset
* Home page: optional second hero button, More Stories link on featured stories
* WordPress Primary menu integration with default menu seeding

= 1.2.0 - June 2026 =
* Local site audit fixes: template hygiene, escaping, 404/search, story card a11y, ACF slug location rules
* Agent documentation in `docs/` and `AGENTS.md`

= 1.0 - September 2025 =
* Initial release

== Credits ==

* Based on Underscores https://underscores.me/, (C) 2012-2020 Automattic, Inc., [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)
* normalize.css https://necolas.github.io/normalize.css/, (C) 2012-2018 Nicolas Gallagher and Jonathan Neal, [MIT](https://opensource.org/licenses/MIT)
