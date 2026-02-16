# NorthCloud Content Integration Design

## Problem

Diidjaaheer has a fully functional NorthCloud article ingestion pipeline (Redis pub/sub subscriber, processing pipeline, Article model, admin dashboard) but:

1. Production Redis config was wrong (`REDIS_HOST=redis`, no password) — subscriber couldn't connect
2. No GitHub deploy key for the repo — fixed alongside CI/CD SSH issues
3. HomeController passes empty arrays for all content props — homepage shows hardcoded placeholders
4. No seeders or factories for dev environments
5. No admin CRUD for events, groups, or teachings

## Fixes Already Applied

- **Production .env**: `REDIS_HOST=127.0.0.1`, password set, explicit `NORTHCLOUD_REDIS_*` vars added
- **Channels**: Changed to `articles:anishinaabe,articles:default` (default is temporary until anishinaabe channel is publishing)
- **`.env.example`**: Updated with correct defaults
- **Subscriber**: Verified connected and ingesting (test article confirmed end-to-end)

## Design

### 1. Wire HomeController to Query Real Data

Update `HomeController` to query the database instead of returning empty arrays:

- **`latestNews`**: `Article::published()->latest('published_at')->take(6)` — map to title, category (first tag), source (newsSource name), time (relative), url, image_url
- **`featuredStories`**: `Article::published()->featured()->latest()->take(3)`, fall back to latest if none featured
- **`powwowEvents`**: `Event::where('starts_at', '>=', now())->orderBy('starts_at')->take(6)` — map day, month, name, location
- **`groups`**: `Group::take(4)` — fields already match the Vue component
- **`teachings`** and **`languageResources`**: Keep as static content in Vue (they're category cards, not dynamic records)

The Vue component already handles graceful fallback (`latestNews.length ? latestNews : placeholderNews`).

### 2. Factories & Seeders

- **`EventFactory`**: Powwow events, cultural festivals, gatherings with Anishinaabe community names, Ontario/Manitoba/Great Lakes locations, future dates
- **`GroupFactory`**: Community organizations with types (Cultural, Language, Youth, Elders) and regions
- **`TeachingFactory`**: Teachings with types matching existing categories (Culture, History, Language)
- **`DatabaseSeeder`**: Seed ~6 events, ~4 groups, ~6 teachings in dev

### 3. Admin CRUD for Events, Groups, Teachings

Resource controllers + Inertia pages in the dashboard:

- `EventController` — CRUD at `/dashboard/events`
- `GroupController` — CRUD at `/dashboard/groups`
- `TeachingController` — CRUD at `/dashboard/teachings`

Each gets Index/Create/Edit Vue pages following NorthCloud article admin page patterns. Protected by auth middleware.

### 4. Future: Article History Replay (TODO)

Investigate nc-http-proxy and NorthCloud Go service for backfilling historical articles instead of waiting for new pub/sub messages.
