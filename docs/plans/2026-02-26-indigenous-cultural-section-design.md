# Indigenous Cultural Section Design

**Date:** 2026-02-26
**Status:** Approved

## Overview

Diidjaaheer expands from an Anishinaabe-focused platform to a hybrid Indigenous cultural knowledge system. Three interconnected layers — cultural, political, and territorial — let users navigate between land, culture, and nation without losing context.

The public experience is map-first with culture and nation views, all staying in sync. Implementation is phased, starting with the cultural tree and public pages.

## Architecture: Three-Layer Hybrid Model

### Cultural Model (Tree)

A self-referencing tree for cultural lineage: Indigenous > Cultural families > Sub-groups > Communities > Clans.

Backbone for teachings, language, stories, and lineage.

```
cultural_groups
├── id (PK)
├── parent_id (FK → self, nullable)
├── name (string)
├── slug (string, unique)
├── depth_type (string: root|family|group|sub_group|community|clan)
├── description (text, nullable)
├── media_id (FK → media, nullable)
├── metadata (JSON, nullable)
├── sort_order (integer, default 0)
└── timestamps
```

Example:
```
Indigenous (root)
└── Anishinaabe (family)
    ├── Ojibwe (group)
    │   ├── Saulteaux (sub_group)
    │   └── Mississauga (sub_group)
    ├── Odawa (group)
    └── Potawatomi (group)
```

`depth_type` is advisory — it helps the UI but does not constrain tree structure.

### Political Model (Graph)

Political identity is separate from cultural identity. An adjacency-list graph where communities can belong to multiple political structures.

```
political_entities
├── id (PK)
├── name (string)
├── slug (string, unique)
├── type (string: first_nation|tribal_nation|treaty_group|band_council|tribal_council|governance_body)
├── description (text, nullable)
├── metadata (JSON, nullable)
└── timestamps

political_relationships
├── id (PK)
├── entity_id (FK → political_entities)
├── related_entity_id (FK → political_entities)
├── relationship_type (string: member_of|governed_by|allied_with|signatory_to|successor_of)
├── metadata (JSON, nullable)
└── timestamps
```

### Territorial Model (Geospatial)

Territory as a third dimension, represented geospatially. Uses GeoJSON stored in JSON columns (SQLite-compatible for dev). Migrate to PostgreSQL + PostGIS when server-side spatial queries become necessary.

```
territories
├── id (PK)
├── name (string)
├── slug (string, unique)
├── type (string: traditional|treaty|reserve|migration_route|shared_region)
├── description (text, nullable)
├── geometry (JSON — GeoJSON Feature)
├── metadata (JSON, nullable)
├── center_lat (decimal, nullable)
├── center_lng (decimal, nullable)
└── timestamps
```

### Join Tables (Cross-Layer Connections)

```
cultural_group_political_entity
├── id (PK)
├── cultural_group_id (FK → cultural_groups)
├── political_entity_id (FK → political_entities)
├── relationship_type (string: represented_by|recognized_as|affiliated_with)
├── metadata (JSON, nullable)
└── timestamps

cultural_group_territory
├── id (PK)
├── cultural_group_id (FK → cultural_groups)
├── territory_id (FK → territories)
├── relationship_type (string: homeland|historical|shared|migration)
├── metadata (JSON, nullable)
└── timestamps

political_entity_territory
├── id (PK)
├── political_entity_id (FK → political_entities)
├── territory_id (FK → territories)
├── relationship_type (string: governs|treaty_area|reserve|claimed)
├── metadata (JSON, nullable)
└── timestamps
```

### Modified Existing Tables

```
teachings (add column)
├── cultural_group_id (FK → cultural_groups, nullable)
```

## Routes

### Public

```
GET  /culture                          — Culture landing (tree root)
GET  /culture/{slug}                   — Cultural group detail
GET  /culture/{slug}/teachings         — Teachings by cultural group
GET  /culture/{slug}/teachings/{slug}  — Single teaching

GET  /nations                          — Nation view landing
GET  /nations/{slug}                   — Political entity detail

GET  /map                              — Full-screen immersive map
GET  /map/territory/{slug}             — Territory detail (split-view)

GET  /teachings                        — All teachings browseable
GET  /teachings/{slug}                 — Single teaching (direct link)
```

### Dashboard (Admin)

```
/dashboard/cultural-groups             — CRUD for cultural tree
/dashboard/political-entities          — CRUD for political entities
/dashboard/territories                 — CRUD for territories
/dashboard/teachings                   — Existing, add cultural_group_id
```

## UI Architecture

### Map Engine

MapLibre GL JS (open-source, no API key). Vue wrapper: `vue-maplibre-gl`. Tile source: OpenStreetMap-based free tiles.

### Map Modes (Single Component)

One `MapView.vue` component with a `mode` prop:

| Mode | Location | Behavior |
|------|----------|----------|
| `immersive` | `/map` | Full viewport, territories as polygons, groups as markers |
| `split` | `/map/territory/{slug}` | Map left, content panel right |
| `embedded` | `/culture/{slug}` | Small sidebar map showing related territories |

### View Switching

Persistent top-level navigation: `[Map] [Culture] [Nations]`

Context preserved via URL query params:
- `/culture/anishinaabe` → Map → `/map?focus=anishinaabe`
- `/map?focus=anishinaabe` → Nations → `/nations?culture=anishinaabe`

### Cultural Page Layout

```
┌──────────────────────────────────────────┐
│  Site Header / Nav  [Map][Culture][Nations]│
├──────────────────────────────────────────┤
│  Breadcrumb: Indigenous > Anishinaabe    │
├───────────────────────┬──────────────────┤
│  Main Content         │  Sidebar:        │
│  - Group Info         │  - Mini Map      │
│  - Teachings Grid     │  - Related Nations│
│  - History Timeline   │  - Child Groups  │
└───────────────────────┴──────────────────┘
```

## Phasing

### Phase 1 — MVP: Cultural Tree + Public Pages

- `cultural_groups` table, model, and self-referencing tree
- Add `cultural_group_id` FK to `teachings`
- Dashboard CRUD for cultural groups
- Update teaching forms to select cultural group
- Public `/culture` landing page
- Public `/culture/{slug}` detail page with teachings
- Public `/teachings` and `/teachings/{slug}` pages
- Seed Anishinaabe data (root → Ojibwe, Odawa, Potawatomi)
- Update homepage framing from "Anishinaabe" to "Indigenous"

### Phase 2 — Political Model + Nation View

- `political_entities` and `political_relationships` tables
- `cultural_group_political_entity` join table
- Dashboard CRUD for political entities
- Public `/nations` pages
- View switching between Culture and Nations

### Phase 3 — Territorial Model + Map

- `territories` table with GeoJSON
- Remaining join tables
- MapLibre integration with three map modes
- Full hybrid navigation (map ↔ culture ↔ nation)

### Phase 4 — Scale + Polish

- PostgreSQL + PostGIS migration
- Cross-layer search
- Community contributions and moderation
- Extended content types (stories, language lessons, media galleries)
