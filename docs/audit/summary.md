# Audit Summary — Goshen Democrats (Local)

**Status:** Audit complete (Phases 0–6)  
**Scope:** Local site only — recent production restore at `http://goshen-democrats.local`  
**Date:** June 15, 2026

---

## Executive summary

The Goshen Democrats site is **functional and content-rich** — 22 stories, an active calendar, a working contact form with spam protection, and a clear navigation structure. The custom theme (`goshendems`) delivers ACF-driven pages and a flexible story content system that works end-to-end.

The audit found **no critical security vulnerabilities** in the theme repo, but identified **46 open items** across code quality, SEO, UX, and incomplete features. The most impactful issues cluster in three areas:

1. **Theme hygiene** — broken `page.php`, unclosed HTML, orphaned JS, inconsistent escaping
2. **SEO overlap** — duplicate Open Graph tags (Yoast + theme); 21/22 stories missing meta descriptions
3. **Stale/incomplete features** — elected-positions CPT dormant but theme templates exist; "building this site" copy outdated

The site is suitable for continued development. Agent documentation in `docs/` and `AGENTS.md` is ready for Cursor and other AI tools.

---

## Scorecard

| Severity | Count |
|----------|-------|
| Critical | 0 |
| High | 9 |
| Medium | 24 |
| Low | 13 |
| **Total open** | **46** |

| Area audited | Status |
|--------------|--------|
| Local inventory & architecture | Complete |
| Theme code & security patterns | Complete |
| ACF & content model | Complete |
| Plugins (Ninja Forms, Yoast, cache) | Complete |
| Security (local) | Complete |
| UX journeys (browser) | Complete |
| Agent documentation | Complete |

---

## What's working well

- **Stories** — CPT archive, pagination, flexible content blocks, featured stories on home
- **Contact form** — Ninja Forms with Turnstile, 42 submissions, email notifications
- **Calendar** — Simple Calendar with active June 2026 events
- **Mobile nav** — Hamburger menu with `aria-expanded`
- **Local dev** — Theme symlinked to repo; MCP connected to Cursor
- **Accessibility basics** — Skip link, semantic regions, labeled form fields
- **Agent docs** — Full `docs/` tree + `AGENTS.md` + Cursor rules

---

## Prioritized fix backlog

Use this as the starting point for your todo list. Items are ordered by impact; **Effort: S/M/L**.

### Tier 1 — Quick wins (do first)

These are small, high-impact fixes with no product decisions required.

| # | Task | Area | Effort |
|---|------|------|--------|
| 1 | Remove orphaned testimonial JS from `page-home.php` (fixes console error) | Theme | S |
| 2 | Add `</main>` before footer on `page-home.php` and `single-story.php` | Theme | S |
| 3 | Remove duplicate OG tags — delete `goshendems_opengraph_tags()` hook; let Yoast own social meta | Theme/SEO | S |
| 4 | Use ACF `form_shortcode` in `page-contact-us.php` instead of hardcoded `[ninja_form id=1]` | Theme | S |
| 5 | Add `rel="noopener noreferrer"` to Donate link in `header.php` | Theme | S |
| 6 | Add null guards on `page-home.php` hero/about and `page-calendar.php` calendar field | Theme | S |
| 7 | Fix `page-about.php` — use `the_title()` not `echo the_title()` | Theme | S |

### Tier 2 — Important improvements

| # | Task | Area | Effort |
|---|------|------|--------|
| 8 | Fix `page.php` — proper page loop or redirect; remove stray `<!doctype>` | Theme | S |
| 9 | Escaping pass on templates (see escaping table in [findings.md](findings.md)) | Theme/Security | M |
| 10 | Custom `404.php` — on-brand message + links to Home, Stories, Calendar, Contact | Theme/UX | S |
| 11 | Clean up `search.php` — remove sidebar/widgets; style like story archive | Theme/UX | M |
| 12 | Restructure story cards — fix invalid HTML + shorten link accessible names | Theme/A11y | M |
| 13 | Migrate ACF page location rules from IDs to slugs (see [content-model.md](../content-model.md)) | ACF | M |
| 14 | Add Yoast meta descriptions to 21 stories missing them | Content/SEO | M |
| 15 | Update home + about ACF copy — remove "building this site" language | Content/UX | S |

### Tier 3 — Product decisions required

Pick a direction before implementing.

| # | Task | Options | Effort |
|---|------|---------|--------|
| 16 | **Elected Positions feature** | A) Activate CPT + field group + enqueue filter JS, or B) Remove archive template + JS until launch | S–M |
| 17 | **Signup / newsletter bar** | A) Wire `signup-bar.php` to Ninja Forms/mailing list, B) Delete orphan partial + CSS | M |
| 18 | **Primary navigation** | A) Migrate to WP Menus (`wp_nav_menu`), B) Keep hardcoded and document | M |
| 19 | **Story CTA blocks** | Audit stories with raw URL link text; set proper `button_label` in ACF | S |
| 20 | **Clear misleading Posts page setting** (page ID 54) in WP admin | Admin | S |

### Tier 4 — Security & admin hygiene

| # | Task | Area | Effort |
|---|------|------|--------|
| 21 | Disable XML-RPC if unused | Security | S |
| 22 | Disable or redirect author archive enumeration | Security | S |
| 23 | Audit 6 WP user accounts — roles, remove unused | Admin | S |
| 24 | Set default comment status to closed | Admin | S |
| 25 | Deactivate DreamHost Panel Login on local (optional) | Admin | S |
| 26 | Run `composer install` to enable PHPCS | DevEx | S |
| 27 | Update stale theme metadata in `style.css` / `readme.txt` (WP 7, PHP 8.4) | Theme | S |

### Tier 5 — Defer / production only

Not in scope for local audit fixes; address before or during prod deploy.

| Item | Notes |
|------|-------|
| Cloudflare production rules | Plugin active locally; rules differ on prod |
| Production MCP setup | HTTP + limited user when ready |
| WP Super Cache tuning for prod | Verify form/cache exclusions on live site |
| Yoast dashboard CPT configuration | Make story scores visible in admin overview |

---

## Decision log (needed before Tier 3)

| Decision | Question | Recommendation |
|----------|----------|----------------|
| Elected Positions | Launch soon or shelve? | If not within 3 months, remove dead code to reduce confusion |
| Newsletter signup | Real feature or cut? | Integrate with existing Ninja Form mailing list checkbox flow |
| Nav management | Editors need nav control? | WP Menus if yes; keep hardcoded if nav rarely changes |

---

## Documentation index

| Doc | Purpose |
|-----|---------|
| [AGENTS.md](../../AGENTS.md) | **Start here** — agent index |
| [architecture.md](../architecture.md) | Stack, URLs, plugins |
| [local-environment.md](../local-environment.md) | Local WP, MCP, WP-CLI |
| [content-model.md](../content-model.md) | ACF fields, CPTs, pages |
| [theme-guide.md](../theme-guide.md) | Templates, conventions |
| [plugins.md](../plugins.md) | Plugin integrations |
| [user-journeys.md](../user-journeys.md) | UX journey test results |
| [findings.md](findings.md) | Full issue log (46 items) |
| [changelog.md](changelog.md) | Doc update history |
| [how-to/](../how-to/) | Extension guides |

---

## Audit phases completed

| Phase | Focus | Key output |
|-------|-------|------------|
| 0 | Local inventory | Doc scaffold, site map |
| 1 | Theme code | Escaping table, structural bugs |
| 2 | ACF & content | Location rule migration plan |
| 3 | Plugins | Ninja Forms, Yoast, cache audit |
| 4 | Security | XML-RPC, enumeration, users |
| 5 | UX journeys | Browser testing, user-journeys.md |
| 6 | Assembly | This summary + prioritized backlog |

---

*Next step: Work through Tier 1 quick wins, then Tier 2. Resolve Tier 3 decisions before implementing those items.*
