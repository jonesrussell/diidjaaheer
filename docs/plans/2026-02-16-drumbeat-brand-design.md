# Drumbeat Brand & Public Pages Design

**Date:** 2026-02-16
**Direction:** "Drumbeat" — Bold Editorial Magazine
**Scope:** Public-facing pages first (homepage, nav, footer, brand system)
**Stack:** Laravel 12, Vue 3, Inertia.js 2, shadcn-vue (reka-ui), Tailwind CSS 4

## Brand Personality

Vibrant & alive. The energy of powwow, living culture, community in motion. A modern digital magazine that pulses with energy — editorial authority meets cultural vibrancy.

## Color Palette

Fresh palette, culturally connected but not tied to Four Directions.

### Light Mode

| Token | Value | Usage |
|-------|-------|-------|
| `background` | `#faf6ed` (warm cream) | Page background |
| `foreground` | `#1a1615` (warm black) | Body text |
| `primary` | `#c4622d` (copper) | CTAs, links, key actions |
| `primary-foreground` | `#faf6ed` | Text on primary |
| `secondary` | `#f0e8d8` (light tan) | Card backgrounds, subtle areas |
| `secondary-foreground` | `#1a1615` | Text on secondary |
| `accent` | `#e8a917` (goldenrod) | Highlights, badges, energy |
| `accent-foreground` | `#1a1615` | Text on accent |
| `muted` | `#e8dfcf` (warm gray) | Disabled, secondary text backgrounds |
| `muted-foreground` | `#7a7067` | Secondary text |
| `sage` | `#5a7a5e` | Nature/culture/language accents |
| `border` | `#ddd4c4` | Borders |
| `input` | `#ddd4c4` | Input borders |
| `ring` | `#c4622d` | Focus rings |
| `destructive` | `#c42d2d` | Errors |
| `destructive-foreground` | `#faf6ed` | Text on destructive |

### Dark Mode

| Token | Value | Usage |
|-------|-------|-------|
| `background` | `#0f0d1a` (indigo-black) | Page background |
| `foreground` | `#f5f0e6` (light cream) | Body text |
| `primary` | `#d4793f` (lighter copper) | CTAs, links |
| `primary-foreground` | `#0f0d1a` | Text on primary |
| `secondary` | `#1a1725` (dark indigo) | Card backgrounds |
| `secondary-foreground` | `#f5f0e6` | Text on secondary |
| `accent` | `#edb525` (bright gold) | Highlights, badges |
| `accent-foreground` | `#0f0d1a` | Text on accent |
| `muted` | `#1f1b2a` (muted indigo) | Disabled areas |
| `muted-foreground` | `#8a8290` (cool gray) | Secondary text |
| `sage` | `#6b9470` | Nature/culture accents |
| `border` | `#2a2535` | Borders |
| `input` | `#2a2535` | Input borders |
| `ring` | `#d4793f` | Focus rings |
| `destructive` | `#e04545` | Errors |
| `destructive-foreground` | `#f5f0e6` | Text on destructive |

## Typography

- **Headings:** DM Serif Display (400, 700) via fonts.bunny.net
- **Body:** Source Sans 3 (400, 500, 600) via fonts.bunny.net
- **Border radius:** 0.5rem (crisper editorial feel)

## Navigation

- Sticky header with backdrop blur
- Left: "Diidjaaheer" wordmark in DM Serif Display, copper color
- Center: Category links (News, Events, Teachings, Community, Language)
- Right: Login / Register
- Mobile: hamburger opens slide-out sheet/drawer
- Subtle shadow on scroll

## Hero Section

- Full-width warm cream background with subtle noise texture overlay
- Large staggered headline in DM Serif Display (text-5xl to text-8xl)
  - Line 1: "Anishinaabe"
  - Line 2: "News, Culture &"
  - Line 3: "Community"
  - Each line staggers in with animation-delay
- Copper accent rule beneath headline
- Subtitle in Source Sans 3, muted-foreground
- Category pill anchors: News, Powwows, Teachings, Groups, Language

## News Section

- Goldenrod vertical accent bar on section heading
- Featured article: 2-col spanning card with gradient mesh placeholder
- 2 smaller cards beside it
- Cards: category badge, title, source, timestamp
- Hover: lift with shadow transition

## Powwow Calendar

- Scrollable row (mobile) / 3-col grid (desktop)
- Date prominent (large day + month), event name, location
- Goldenrod left-border accent

## Cultural Teachings

- 3-col grid
- Colored top-border (rotating copper/goldenrod/sage)
- Lucide icon at top (BookOpen, History, Languages)
- Title + description

## Community Groups

- 2-col desktop, stacked mobile
- Name, type badge, region, external link
- Sage green accent

## Language Resources

- Single featured card with sage green tint
- "Ojibwe / Anishinaabemowin" heading

## Footer

- Dark background with light text
- 3-col: Brand + tagline | Quick links | Community links
- Copper accent line at top
- Bottom bar with copyright

## Animations (CSS-only)

- Page load: fade-in-up with staggered delays per section
- Cards: hover lift with shadow
- Nav: backdrop-blur transition on scroll
- Section headings: subtle slide-in from left

## Files to Modify

1. `resources/views/app.blade.php` — fonts, bg colors
2. `resources/css/app.css` — full palette rewrite, typography tokens
3. `resources/js/layouts/PublicLayout.vue` — nav redesign with mobile drawer
4. `resources/js/pages/Home/Index.vue` — full homepage redesign
5. `resources/js/components/AppLogo.vue` — wordmark rebrand
6. `resources/js/components/AppLogoIcon.vue` — remove Laravel logo
