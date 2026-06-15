# Documentation Changelog

Track documentation updates tied to audit phases.

---

## 2026-06-15 — Resources CPT

**Added:**

- `resource` CPT with archive at `/resources/`
- `resource-category` taxonomy with client-side archive filtering
- ACF options page for intro copy and manual resource order
- `archive-resource.php`, `inc/resources.php`, `js/resources-filter.js`

**Updated:** `docs/content-model.md`, Primary nav default Resources URL

---

## 2026-06-15 — Phase 6: Audit complete

**Audit phase:** Phase 6 complete — **full audit finished**

**Created:**

| File | Description |
|------|-------------|
| `docs/audit/summary.md` | Executive summary, scorecard, prioritized fix backlog (Tiers 1–5) |

**Updated:**

| File | Changes |
|------|---------|
| `AGENTS.md` | Audit marked complete; link to summary/backlog |
| `docs/audit/findings.md` | Phase 6 section closed |

**Final scorecard:** 0 Critical · 9 High · 24 Medium · 13 Low (46 open)

**Next:** Work through fix backlog in [summary.md](summary.md) — start with Tier 1 quick wins.

---

## 2026-06-15 — Phase 5: Usability & UX journeys

**Audit phase:** Phase 5 complete (browser testing desktop + mobile)

**Created/updated:**

| File | Changes |
|------|---------|
| `docs/user-journeys.md` | Full journey map, nav reference, UX notes |
| `docs/audit/findings.md` | +8 UX findings, positives documented |
| `AGENTS.md` | Added user-journeys to doc map; Phase 5 complete |

**Journey results:**

| Journey | Result |
|---------|--------|
| Home → Stories → Story | Pass |
| Home → Contact (Join Now) | Pass |
| Calendar | Pass — active events |
| Contact form | Pass — fields render |
| Mobile menu | Pass |
| Search `?s=trivia` | Partial — works, _s UI |
| 404 | Partial — generic template |
| Elected positions | Fail — 404 |

**Top UX issues:** Stale "building this site" copy; story card a11y; home JS error; uncustomized 404/search

**Next:** Phase 6 — final doc assembly + prioritized fix backlog

---

## 2026-06-15 — Phase 3 & 4: Plugins + security

**Audit phases:** Phase 3 and Phase 4 complete

**Updated:**

| File | Changes |
|------|---------|
| `docs/plugins.md` | Full Ninja Forms, Yoast, cache, SMTP audit data |
| `docs/audit/findings.md` | +10 findings; positives documented |
| `docs/architecture.md` | Story URL canonical vs redirect clarified |

**Key discoveries:**

- Duplicate `og:title` on every page (Yoast + theme) — **High**
- 21/22 stories missing meta descriptions; avg readability 51.8
- Ninja Forms: Turnstile active, 42 submissions, 4 actions working
- WP Super Cache active (super cache + mod_rewrite)
- XML-RPC enabled; author enumeration reveals `alex` username
- Story singles canonical at `/story/slug/`; `/stories/slug/` redirects

**Next:** Phase 5 (usability journeys) + Phase 6 (final doc assembly)

---

## 2026-06-15 — Phase 1 & 2: Theme code + ACF/content model

**Audit phases:** Phase 1 and Phase 2 complete

**Updated:**

| File | Changes |
|------|---------|
| `docs/audit/findings.md` | +17 findings; escaping audit table; summary counts |
| `docs/content-model.md` | ACF location migration table; elected-positions dormant status |
| `docs/theme-guide.md` | Template checklist; Phase 1 debt summary |
| `docs/architecture.md` | `/stories/` routing clarified |

**Key discoveries:**

- `page.php` broken; unclosed `<main>` on home/single-story
- Home page JS error (testimonial script without DOM)
- 26 open findings (6 High, 12 Medium, 8 Low)
- Elected Positions: fully dormant (0 posts, CPT inactive)
- `/stories/` = CPT archive (works); Posts page setting misleading
- Signup bar partial exists but is never included

**Next:** Phase 3 (plugins — Ninja Forms, Yoast, cache overlap)

---

## 2026-06-15 — Phase 0: Local inventory & scaffold

**Audit phase:** Phase 0 complete

**Created:**

| File | Description |
|------|-------------|
| `AGENTS.md` | Master index for AI agents |
| `docs/architecture.md` | Stack, URL map, plugin list |
| `docs/local-environment.md` | Local WP paths, MCP, WP-CLI |
| `docs/content-model.md` | Pages, CPTs, ACF field reference |
| `docs/theme-guide.md` | Templates, conventions, enqueues |
| `docs/plugins.md` | Plugin integration overview |
| `docs/audit/findings.md` | Initial findings from inventory |
| `docs/how-to/add-story-block.md` | How-to stub |
| `docs/how-to/add-page-template.md` | How-to stub |
| `docs/how-to/add-acf-field-group.md` | How-to stub |
| `docs/how-to/add-cpt.md` | How-to stub |
| `.cursor/rules/goshendems-theme.mdc` | Cursor rule for theme edits |

**Inventory captured:**

- WordPress 7.0, PHP 8.4.10, MySQL 8.0.35
- 5 pages, 22+ stories, 15 active plugins
- Theme symlink confirmed to repo
- Ninja Forms: Contact Us (ID 1)
- ACF JSON: 9 files; elected-positions inactive

**Next:** Phase 1 (theme code audit) + Phase 2 (ACF deep dive)
