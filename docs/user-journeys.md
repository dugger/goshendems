# User Journeys — Goshen Democrats (Local)

Phase 5 UX audit on `http://goshen-democrats.local` (desktop + 375px mobile).

---

## Journey map

| # | Journey | Path | Result |
|---|---------|------|--------|
| 1 | New visitor → learn → read story | Home → Stories → Story single | **Pass** |
| 2 | Get involved | Home hero "Join Now" → Contact | **Pass** |
| 3 | Contact the party | Nav → Contact → Form | **Pass** |
| 4 | Find events | Nav → Calendar | **Pass** |
| 5 | Donate | Nav → Donate (ActBlue, new tab) | **Pass** |
| 6 | Search content | `/?s=trivia` | **Partial** — works but UI is _s boilerplate |
| 7 | Elected positions | `/elected-positions/` | **Fail** — 404 (CPT inactive) |
| 8 | Bad URL | `/this-page-does-not-exist/` | **Partial** — generic 404, not on-brand |
| 9 | Mobile navigation | Home → Toggle menu | **Pass** |

---

## Journey 1: Home → Stories → Story

### Home (`/`)
- **Hero:** "Get Involved!" + "Join Now" CTA → `/contact-us/`
- **About blurb:** "We are currently building this site…" — **stale copy** (site has 22 stories, calendar, contact form)
- **Featured stories:** 3 cards with images and excerpts
- **Skip link:** present
- **JS error on load:** testimonial script references `#prev` / `#next` that don't exist (HTML commented out)

### Stories archive (`/stories/`)
- Grid of 9 story cards per page
- Pagination: pages 2, 3, Next — **works**
- Page title: "Stories"

### Story single (`/story/{slug}/`)
- Example: `/story/trivia-party-on-6-13/`
- Hero image, title, date, flexible content blocks render
- CTA block: heading + **raw Google Forms URL** as link text (poor UX/a11y)
- Content readable; multiple paragraphs + CTA

### Accessibility notes
- Story cards: each card is one `<a>` whose accessible name includes **title + date + full excerpt** — very long, confusing for screen readers
- Invalid HTML: `<a>` wraps `<article>` (documented in Phase 1)

---

## Journey 2: Contact form (`/contact-us/`)

### What works
- Form intro text from ACF renders
- Fields: Name*, Email*, Mailing list checkbox (default checked), Phone, Message, Submit
- Labels associated with inputs
- Cloudflare Turnstile field present (may render in iframe)

### Not tested
- Full submission (would create real submission + email — skipped to avoid polluting prod data on local restore)

### Agent note
Form is Ninja Form ID 1, hardcoded in template. Turnstile spam protection active.

---

## Journey 3: Calendar (`/calendar/`)

### What works
- Simple Calendar renders **June 2026** with many events
- Previous/Next month buttons labeled
- Event list includes GCDP meetings, city council, county commissioners, community events
- Region labeled "Calendar"

### UX notes
- Event descriptions can be very long (URLs, agenda links inline) — heavy for screen reader users
- Calendar is a **high-value page** — works well

---

## Journey 4: About (`/about/`)

- Hero image + WYSIWYG body
- Same "building this site" language as home — **update recommended**
- Points users to Events page
- Single H1: "About Goshen City Democratic Party"

---

## Journey 5: Search (`/?s=trivia`)

### What works
- Returns 2 relevant stories: "Trivia Party on 6/13", "Shellions Win Trivia Night"

### UX gaps
- Uses default `search.php` + `content-search.php` (_s boilerplate)
- Sidebar widgets: Recent Posts, Recent Comments, Archives, Categories — all **empty/useless** (no native posts)
- No search box in primary navigation — search only on 404/results pages

---

## Journey 6: 404 (`/this-page-does-not-exist/`)

- Default WordPress message: "Oops! That page can't be found."
- Includes search form + empty category/archive widgets
- **Not on-brand** — should link to Home, Stories, Calendar, Contact instead
- `404.php` is uncustomized Underscores template

---

## Journey 7: Elected positions (`/elected-positions/`)

- Returns **404** — expected (CPT inactive, 0 posts)
- Archive template exists in theme but is unreachable
- **Do not link to this URL** until CPT activated

---

## Journey 8: Mobile (375px)

| Feature | Status |
|---------|--------|
| Hamburger menu button | Visible, `aria-expanded` toggles |
| Menu items on expand | About, Events, Stories, Contact, Donate |
| Hero readable | Yes |
| Featured stories stack | Yes |

Mobile navigation **works**.

---

## Navigation reference

Hardcoded in `header.php`:

| Label | URL | Notes |
|-------|-----|-------|
| About | `/about` | |
| Events | `/calendar` | Label says "Events", slug is `calendar` |
| Stories | `/stories` | CPT archive |
| Contact | `/contact-us` | |
| Donate | ActBlue (external) | `target="_blank"` |

Footer quick links: Events, Stories + email `goshendemocraticparty@gmail.com`

**No site search in nav.**

---

## Positive UX patterns

- Clear primary nav labels
- Skip to content link
- Semantic regions (`Hero banner`, `Featured Stories`, `Calendar`, `Footer`)
- Story archive pagination
- Contact form with mailing list opt-in
- Calendar is actively maintained with real events
- Mobile menu accessible

---

## Priority UX improvements

See [audit/findings.md](audit/findings.md) Phase 5 section for tracked issues.

| Priority | Issue |
|----------|-------|
| High | Stale "building this site" copy on home + about |
| High | Story card link accessible names too long |
| High | Home page JS console error |
| Medium | 404 page not customized |
| Medium | Search results page _s boilerplate |
| Medium | CTA blocks showing raw URLs as link text |
| Medium | No search in navigation |
| Low | Events nav label vs `/calendar` slug mismatch |
| Low | Signup/join bar not present on site |

---

## Related docs

- [architecture.md](architecture.md) — URL map
- [content-model.md](content-model.md) — pages and fields
- [plugins.md](plugins.md) — forms, calendar, SEO
- [audit/findings.md](audit/findings.md)
