# Drumbeat Brand Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Implement the "Drumbeat" brand system and redesign all public-facing pages for diidjaaheer.live

**Architecture:** Replace the current Laravel starter kit styling with a custom editorial magazine design. Modify CSS tokens, blade template fonts, public layout, homepage, and logo components. All changes use existing shadcn-vue components and Tailwind CSS 4.

**Tech Stack:** Laravel 12, Vue 3, Inertia.js 2, shadcn-vue (reka-ui), Tailwind CSS 4, Lucide icons

**Design Reference:** `docs/plans/2026-02-16-drumbeat-brand-design.md`

---

### Task 1: Update fonts in app.blade.php

**Files:**
- Modify: `resources/views/app.blade.php`

**Step 1: Replace font link and background colors**

Replace the existing Instrument Sans font link and inline background styles with DM Serif Display + Source Sans 3 from fonts.bunny.net. Update the inline `<style>` background colors to match the new palette.

Change line 40:
```html
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
```
To:
```html
<link href="https://fonts.bunny.net/css?family=dm-serif-display:400&family=source-sans-3:400,500,600" rel="stylesheet" />
```

Change inline `<style>` block (lines 23-31):
```html
<style>
    html {
        background-color: #faf6ed;
    }
    html.dark {
        background-color: #0f0d1a;
    }
</style>
```

**Step 2: Update Inertia progress bar color**

In `resources/js/app.ts` line 24, change:
```ts
color: '#4B5563',
```
To:
```ts
color: '#c4622d',
```

**Step 3: Verify by running the dev server**

Run: `npm run build`
Expected: Build succeeds with no errors

**Step 4: Commit**

```bash
git add resources/views/app.blade.php resources/js/app.ts
git commit -m "feat: update fonts to DM Serif Display + Source Sans 3 and new brand colors"
```

---

### Task 2: Rewrite CSS design tokens

**Files:**
- Modify: `resources/css/app.css`

**Step 1: Replace the entire CSS file**

Replace the full contents of `resources/css/app.css` with the new Drumbeat palette. Key changes:
- Font family: `DM Serif Display` for headings (via `--font-serif`), `Source Sans 3` for body (via `--font-sans`)
- All color tokens updated to the Drumbeat palette (copper, goldenrod, sage, warm cream, indigo-black)
- Border radius reduced to `0.5rem`
- Custom utility classes for section accents, noise texture, and staggered animations
- Light and dark mode fully defined

The `:root` block must set:
```css
--background: #faf6ed;
--foreground: #1a1615;
--primary: #c4622d;
--primary-foreground: #faf6ed;
--secondary: #f0e8d8;
--secondary-foreground: #1a1615;
--accent: #e8a917;
--accent-foreground: #1a1615;
--muted: #e8dfcf;
--muted-foreground: #7a7067;
--border: #ddd4c4;
--input: #ddd4c4;
--ring: #c4622d;
--destructive: #c42d2d;
--destructive-foreground: #faf6ed;
--radius: 0.5rem;
```

The `.dark` block must set:
```css
--background: #0f0d1a;
--foreground: #f5f0e6;
--primary: #d4793f;
--primary-foreground: #0f0d1a;
--secondary: #1a1725;
--secondary-foreground: #f5f0e6;
--accent: #edb525;
--accent-foreground: #0f0d1a;
--muted: #1f1b2a;
--muted-foreground: #8a8290;
--border: #2a2535;
--input: #2a2535;
--ring: #d4793f;
--destructive: #e04545;
--destructive-foreground: #f5f0e6;
```

Add `--font-serif` to the `@theme inline` block:
```css
--font-serif: 'DM Serif Display', serif;
```

Update `--font-sans`:
```css
--font-sans: 'Source Sans 3', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';
```

Add custom card/popover/sidebar tokens that reference the main tokens (keep existing pattern from the file).

Add animation utilities:
```css
@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(1.5rem);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-in-left {
    from {
        opacity: 0;
        transform: translateX(-1rem);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
```

And utility classes:
```css
.animate-fade-in-up {
    animation: fade-in-up 0.6s ease-out both;
}

.animate-slide-in-left {
    animation: slide-in-left 0.5s ease-out both;
}

.noise-texture {
    position: relative;
}

.noise-texture::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: url("data:image/svg+xml,..."); /* inline SVG noise */
    opacity: 0.03;
    pointer-events: none;
}
```

**Step 2: Verify build**

Run: `npm run build`
Expected: Build succeeds with no errors

**Step 3: Commit**

```bash
git add resources/css/app.css
git commit -m "feat: implement Drumbeat color palette and typography tokens"
```

---

### Task 3: Rebrand logo components

**Files:**
- Modify: `resources/js/components/AppLogo.vue`
- Modify: `resources/js/components/AppLogoIcon.vue`

**Step 1: Update AppLogoIcon.vue**

Replace the Laravel SVG logo with a simple "D" lettermark rendered as an SVG text element in DM Serif Display, using `currentColor` for theming:

```vue
<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 40 40"
        :class="className"
        v-bind="$attrs"
    >
        <text
            x="50%"
            y="52%"
            dominant-baseline="central"
            text-anchor="middle"
            fill="currentColor"
            font-family="'DM Serif Display', serif"
            font-size="32"
            font-weight="400"
        >D</text>
    </svg>
</template>
```

Keep the existing script setup block unchanged.

**Step 2: Update AppLogo.vue**

Change "Laravel Starter Kit" to "Diidjaaheer":

```vue
<span class="mb-0.5 truncate leading-tight font-semibold">Diidjaaheer</span>
```

**Step 3: Verify build**

Run: `npm run build`
Expected: Build succeeds

**Step 4: Commit**

```bash
git add resources/js/components/AppLogo.vue resources/js/components/AppLogoIcon.vue
git commit -m "feat: rebrand logo from Laravel Starter Kit to Diidjaaheer"
```

---

### Task 4: Redesign PublicLayout.vue

**Files:**
- Modify: `resources/js/layouts/PublicLayout.vue`

**Step 1: Rewrite the layout**

Replace the current minimal layout with the Drumbeat navigation and footer design:

**Header:**
- Sticky with backdrop blur (`sticky top-0 z-50 bg-background/95 backdrop-blur`)
- Left: "Diidjaaheer" wordmark as an `<Link>` in `font-serif text-xl text-primary`
- Center: Desktop nav links (News, Events, Teachings, Community, Language) as anchor links (`#news`, `#events`, etc.) — hidden on mobile (`hidden lg:flex`)
- Right: Login/Register buttons using shadcn Button component (ghost variant for login, default for register)
- Mobile: Menu icon button that opens a `<Sheet>` from the right with the same nav links

**Footer:**
- Dark background (`bg-foreground text-background`)
- Copper accent line at top (`border-t-2 border-primary`)
- 3-column grid on desktop, stacked on mobile
  - Col 1: "Diidjaaheer" wordmark + tagline
  - Col 2: Quick links (News, Events, Teachings)
  - Col 3: Community links (Groups, Language, About)
- Bottom bar with copyright year

**Imports needed:**
```ts
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login, register } from '@/routes';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { Menu } from 'lucide-vue-next';
```

**Step 2: Verify build**

Run: `npm run build`
Expected: Build succeeds

**Step 3: Commit**

```bash
git add resources/js/layouts/PublicLayout.vue
git commit -m "feat: redesign public layout with Drumbeat nav and footer"
```

---

### Task 5: Redesign Home/Index.vue — Hero + News sections

**Files:**
- Modify: `resources/js/pages/Home/Index.vue`

**Step 1: Rewrite the hero and news sections**

Replace the current placeholder homepage with the Drumbeat design. This task covers the hero and news sections:

**Hero:**
- Full-width section with `noise-texture` class
- Large staggered headline in `font-serif`:
  - `<span class="block animate-fade-in-up" style="animation-delay: 0s">Anishinaabe</span>`
  - `<span class="block animate-fade-in-up" style="animation-delay: 0.15s">News, Culture &</span>`
  - `<span class="block animate-fade-in-up" style="animation-delay: 0.3s">Community</span>`
- Sizing: `text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-serif tracking-tight`
- Copper horizontal rule: `<div class="mx-auto mt-6 h-1 w-24 rounded-full bg-primary" />`
- Subtitle: `<p class="mt-4 text-lg sm:text-xl text-muted-foreground max-w-2xl mx-auto">...</p>`
- Category pills row: flex wrap of rounded pill badges anchoring to sections

**News Section (id="news"):**
- Section heading with goldenrod left accent: `<div class="flex items-center gap-3"><div class="h-8 w-1 rounded-full bg-accent" /><h2 class="font-serif text-3xl">Latest News</h2></div>`
- 3-column grid (responsive: 1 col mobile, 2 col tablet, 3 col desktop)
- Featured card spans 2 columns on desktop with larger height
- Cards use shadcn `Card` with hover effect: `transition-all duration-300 hover:-translate-y-1 hover:shadow-lg`
- Each card: `Badge` for category (variant outline), `CardTitle`, `CardDescription`, timestamp in `text-xs text-muted-foreground`
- Placeholder state (empty arrays): show 3 skeleton-style cards with gradient mesh backgrounds

**Step 2: Verify build**

Run: `npm run build`
Expected: Build succeeds

**Step 3: Commit**

```bash
git add resources/js/pages/Home/Index.vue
git commit -m "feat: implement Drumbeat hero and news sections"
```

---

### Task 6: Redesign Home/Index.vue — Remaining sections

**Files:**
- Modify: `resources/js/pages/Home/Index.vue`

**Step 1: Add Powwow Calendar section (id="events")**

- Section heading with goldenrod accent bar (same pattern as news)
- 3-column responsive grid
- Each event card: large day number (`text-4xl font-serif text-primary`), month below, event name, location with MapPin icon
- Goldenrod left border on cards: `border-l-4 border-accent`
- Placeholder: 3 cards with "Check back soon" messaging

**Step 2: Add Cultural Teachings section (id="teachings")**

- 3-column grid
- Cards with colored top borders rotating: `border-t-4 border-primary` / `border-t-4 border-accent` / `border-t-4 border-[#5a7a5e]`
- Lucide icons at top: `BookOpen`, `History`, `Languages`
- Icon in a rounded background circle
- Title + description placeholder text

**Step 3: Add Community Groups section (id="community")**

- 2-column grid on desktop
- Cards with sage green accent: `border-l-4 border-[#5a7a5e]`
- Group name, type badge, region, external link icon (`ExternalLink` from Lucide)

**Step 4: Add Language Resources section (id="language")**

- Single featured card with sage green tinted background: `bg-[#5a7a5e]/10 dark:bg-[#6b9470]/10`
- "Ojibwe / Anishinaabemowin" heading in font-serif
- Languages icon from Lucide
- Placeholder description text

**Step 5: Verify build**

Run: `npm run build`
Expected: Build succeeds

**Step 6: Commit**

```bash
git add resources/js/pages/Home/Index.vue
git commit -m "feat: implement remaining Drumbeat homepage sections"
```

---

### Task 7: Final verification and cleanup

**Files:**
- Possibly modify: any file with issues found during verification

**Step 1: Full build check**

Run: `npm run build`
Expected: Clean build, zero errors, zero warnings

**Step 2: TypeScript check**

Run: `npx vue-tsc --noEmit`
Expected: No type errors

**Step 3: Lint check**

Run: `npx eslint resources/js/layouts/PublicLayout.vue resources/js/pages/Home/Index.vue resources/js/components/AppLogo.vue resources/js/components/AppLogoIcon.vue`
Expected: No lint errors (fix any that appear)

**Step 4: Remove unused Welcome.vue (optional)**

The old `Welcome.vue` page is the Laravel starter kit default and is now superseded by `Home/Index.vue`. Check if the `/welcome` route in `routes/web.php` is still needed. If it just renders a generic Laravel welcome page, remove both the route and the file.

**Step 5: Commit any fixes**

```bash
git add -A
git commit -m "chore: final cleanup and verification for Drumbeat brand"
```
