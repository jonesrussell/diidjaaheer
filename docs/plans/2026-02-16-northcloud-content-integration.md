# NorthCloud Content Integration Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Replace placeholder content on the homepage with real NorthCloud articles, create seeders/factories for dev, and add admin CRUD for events/groups/teachings.

**Architecture:** HomeController queries Article (via NorthCloud package), Event, Group, and Teaching models and passes data to the Inertia homepage. Admin CRUD controllers follow the NorthCloud ArticleController pattern with Inertia pages using the existing admin components. Factories and seeders provide dev data.

**Tech Stack:** Laravel 12, Pest 4, Vue 3 + Inertia.js 2, shadcn-vue, Tailwind CSS 4

---

### Task 1: Wire HomeController to Query Real Articles

**Files:**
- Modify: `app/Http/Controllers/HomeController.php`
- Test: `tests/Feature/HomeControllerTest.php`

**Step 1: Write the failing test**

```php
<?php

use JonesRussell\NorthCloud\Models\Article;
use JonesRussell\NorthCloud\Models\NewsSource;

test('homepage returns latest published articles', function () {
    $source = NewsSource::create([
        'name' => 'Test Source',
        'slug' => 'test-source',
        'url' => 'https://example.com',
    ]);

    Article::create([
        'title' => 'Published Article',
        'slug' => 'published-article',
        'url' => 'https://example.com/1',
        'external_id' => 'ext-1',
        'news_source_id' => $source->id,
        'status' => 'published',
        'published_at' => now()->subHour(),
    ]);

    Article::create([
        'title' => 'Draft Article',
        'slug' => 'draft-article',
        'url' => 'https://example.com/2',
        'external_id' => 'ext-2',
        'news_source_id' => $source->id,
        'status' => 'draft',
        'published_at' => null,
    ]);

    $response = $this->get('/');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Home/Index')
        ->has('latestNews', 1)
        ->where('latestNews.0.title', 'Published Article')
    );
});

test('homepage returns empty arrays when no content exists', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Home/Index')
        ->has('latestNews', 0)
        ->has('powwowEvents', 0)
        ->has('groups', 0)
    );
});
```

**Step 2: Run test to verify it fails**

Run: `php artisan test --filter=HomeControllerTest`
Expected: FAIL — `assertInertia` will fail because `latestNews` is always `[]`

**Step 3: Implement HomeController**

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Group;
use Inertia\Inertia;
use Inertia\Response;
use JonesRussell\NorthCloud\Models\Article;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Home/Index', [
            'latestNews' => Article::published()
                ->with(['newsSource', 'tags'])
                ->latest('published_at')
                ->take(6)
                ->get()
                ->map(fn (Article $article) => [
                    'id' => $article->id,
                    'title' => $article->title,
                    'category' => $article->tags->first()?->name ?? 'News',
                    'source' => $article->newsSource?->name,
                    'time' => $article->published_at?->diffForHumans(),
                    'url' => $article->url,
                    'image_url' => $article->image_url,
                    'excerpt' => $article->excerpt,
                ]),
            'powwowEvents' => Event::where('starts_at', '>=', now())
                ->orderBy('starts_at')
                ->take(6)
                ->get()
                ->map(fn (Event $event) => [
                    'id' => $event->id,
                    'day' => $event->starts_at->format('d'),
                    'month' => $event->starts_at->format('M'),
                    'name' => $event->title,
                    'location' => $event->location,
                    'type' => $event->type,
                ]),
            'groups' => Group::take(4)
                ->get()
                ->map(fn (Group $group) => [
                    'id' => $group->id,
                    'name' => $group->name,
                    'type' => $group->type,
                    'region' => $group->region,
                    'url' => $group->url,
                ]),
        ]);
    }
}
```

**Step 4: Run test to verify it passes**

Run: `php artisan test --filter=HomeControllerTest`
Expected: PASS

**Step 5: Commit**

```bash
git add app/Http/Controllers/HomeController.php tests/Feature/HomeControllerTest.php
git commit -m "feat: wire HomeController to query real articles, events, and groups"
```

---

### Task 2: Update Home/Index.vue to Display Real Article Data

**Files:**
- Modify: `resources/js/pages/Home/Index.vue`

**Step 1: Update the Props interface and article card rendering**

Update the `Props` interface to match the new data shape from HomeController. Update the news card template to use `image_url`, `url`, and `excerpt`. Remove unused props (`teachings`, `languageResources`, `featuredStories`) and their placeholder data.

Key changes:
- `NewsItem` gets `id`, `url`, `image_url`, `excerpt` fields
- News cards become `<a>` links wrapping the Card when `item.url` exists
- Show `item.image_url` as a background image instead of the gradient placeholder
- Keep gradient as fallback when no `image_url`
- Events/groups use real data with same fallback pattern

**Step 2: Run frontend checks**

Run: `npm run type-check && npm run lint:check`
Expected: PASS

**Step 3: Commit**

```bash
git add resources/js/pages/Home/Index.vue
git commit -m "feat: update homepage to render real article and event data"
```

---

### Task 3: Create Factories for Event, Group, Teaching

**Files:**
- Create: `database/factories/EventFactory.php`
- Create: `database/factories/GroupFactory.php`
- Create: `database/factories/TeachingFactory.php`

**Step 1: Create EventFactory**

```php
<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Event> */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $names = [
            'Spring Traditional Powwow',
            'Gathering of Nations',
            'Anishinaabe Cultural Festival',
            'Ojibwe Language Immersion Camp',
            'Elder Teachings Circle',
            'Youth Drumming Workshop',
            'Treaty Day Celebration',
            'Midewiwin Ceremony',
            'Wild Rice Harvest Gathering',
            'Winter Storytelling Night',
        ];

        $locations = [
            'Sault Ste. Marie, ON',
            'Thunder Bay, ON',
            'Sudbury, ON',
            'Winnipeg, MB',
            'Duluth, MN',
            'Saginaw, MI',
            'Garden River First Nation, ON',
            'Batchewana First Nation, ON',
            'Red Lake, ON',
            'Kenora, ON',
        ];

        $title = fake()->randomElement($names);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 9999),
            'starts_at' => fake()->dateTimeBetween('+1 week', '+6 months'),
            'ends_at' => fn (array $attrs) => fake()->optional(0.7)->dateTimeBetween($attrs['starts_at'], '+1 day'),
            'location' => fake()->randomElement($locations),
            'type' => fake()->randomElement(['powwow', 'gathering', 'ceremony']),
            'description' => fake()->optional()->paragraph(),
        ];
    }
}
```

**Step 2: Create GroupFactory**

```php
<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Group> */
class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition(): array
    {
        $names = [
            'Anishinaabe Cultural Network',
            'Ojibwe Language Society',
            'Great Lakes Indigenous Youth Council',
            'Turtle Island Elders Circle',
            'Anishinaabemowin Revitalization Project',
            'First Nations Women\'s Association',
            'Indigenous Artists Collective',
            'Woodland Drum Circle',
        ];

        $name = fake()->unique()->randomElement($names);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'type' => fake()->randomElement(['online', 'offline']),
            'url' => fake()->optional(0.6)->url(),
            'description' => fake()->optional()->sentence(),
            'region' => fake()->randomElement([
                'Great Lakes',
                'Ontario',
                'Manitoba',
                'Ontario / Manitoba',
                'Minnesota / Wisconsin',
                'Northern Ontario',
            ]),
        ];
    }
}
```

**Step 3: Create TeachingFactory**

```php
<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Teaching;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Teaching> */
class TeachingFactory extends Factory
{
    protected $model = Teaching::class;

    public function definition(): array
    {
        $teachings = [
            'culture' => [
                'Seven Grandfather Teachings',
                'The Medicine Wheel',
                'Smudging Ceremony',
                'Clan System and Governance',
            ],
            'history' => [
                'The Three Fires Confederacy',
                'Treaty of Niagara 1764',
                'Migration from the Eastern Seaboard',
                'The Dish With One Spoon Wampum',
            ],
            'language' => [
                'Introduction to Anishinaabemowin',
                'Everyday Greetings and Phrases',
                'Numbers and Counting',
                'Seasons and Nature Words',
            ],
        ];

        $type = fake()->randomElement(array_keys($teachings));
        $title = fake()->randomElement($teachings[$type]);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 9999),
            'type' => $type,
            'content' => fake()->paragraphs(3, true),
        ];
    }
}
```

**Step 4: Run a quick test to make sure factories work**

Run: `php artisan tinker --execute="App\Models\Event::factory()->make(); echo 'OK';"`
Expected: `OK`

**Step 5: Commit**

```bash
git add database/factories/EventFactory.php database/factories/GroupFactory.php database/factories/TeachingFactory.php
git commit -m "feat: add factories for Event, Group, and Teaching models"
```

---

### Task 4: Update DatabaseSeeder

**Files:**
- Modify: `database/seeders/DatabaseSeeder.php`

**Step 1: Write test for seeder**

```php
// tests/Feature/SeederTest.php
<?php

use App\Models\Event;
use App\Models\Group;
use App\Models\Teaching;

test('database seeder creates events, groups, and teachings', function () {
    $this->seed();

    expect(Event::count())->toBeGreaterThanOrEqual(6);
    expect(Group::count())->toBeGreaterThanOrEqual(4);
    expect(Teaching::count())->toBeGreaterThanOrEqual(6);
});
```

**Step 2: Run test to verify it fails**

Run: `php artisan test --filter=SeederTest`
Expected: FAIL

**Step 3: Update DatabaseSeeder**

```php
<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Group;
use App\Models\Teaching;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Event::factory(6)->create();
        Group::factory(4)->create();
        Teaching::factory(6)->create();
    }
}
```

**Step 4: Run test to verify it passes**

Run: `php artisan test --filter=SeederTest`
Expected: PASS

**Step 5: Commit**

```bash
git add database/seeders/DatabaseSeeder.php tests/Feature/SeederTest.php tests/Feature/HomeControllerTest.php
git commit -m "feat: seed events, groups, and teachings in DatabaseSeeder"
```

---

### Task 5: Add Admin Routes for Events, Groups, Teachings

**Files:**
- Modify: `routes/web.php`
- Create: `app/Http/Controllers/Dashboard/EventController.php`
- Create: `app/Http/Controllers/Dashboard/GroupController.php`
- Create: `app/Http/Controllers/Dashboard/TeachingController.php`

**Step 1: Write failing test for event routes**

```php
// tests/Feature/Dashboard/EventControllerTest.php
<?php

use App\Models\Event;
use App\Models\User;

test('event index requires authentication', function () {
    $this->get('/dashboard/events')->assertRedirect('/login');
});

test('event index returns events', function () {
    $user = User::factory()->create();
    Event::factory(3)->create();

    $this->actingAs($user)
        ->get('/dashboard/events')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/events/Index')
            ->has('events.data', 3)
        );
});

test('event can be created', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/dashboard/events', [
            'title' => 'Spring Powwow',
            'slug' => 'spring-powwow',
            'starts_at' => '2026-06-15 10:00:00',
            'type' => 'powwow',
            'location' => 'Sault Ste. Marie, ON',
        ])
        ->assertRedirect('/dashboard/events');

    expect(Event::where('title', 'Spring Powwow')->exists())->toBeTrue();
});

test('event can be updated', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();

    $this->actingAs($user)
        ->put("/dashboard/events/{$event->id}", [
            'title' => 'Updated Powwow',
            'slug' => $event->slug,
            'starts_at' => '2026-06-15 10:00:00',
            'type' => 'powwow',
        ])
        ->assertRedirect('/dashboard/events');

    expect($event->fresh()->title)->toBe('Updated Powwow');
});

test('event can be deleted', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();

    $this->actingAs($user)
        ->delete("/dashboard/events/{$event->id}")
        ->assertRedirect('/dashboard/events');

    expect(Event::find($event->id))->toBeNull();
});
```

**Step 2: Run test to verify it fails**

Run: `php artisan test --filter=EventControllerTest`
Expected: FAIL — routes don't exist

**Step 3: Create EventController**

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    public function index(Request $request): Response
    {
        $events = Event::query()
            ->when($request->search, fn ($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->orderBy('starts_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('dashboard/events/Index', [
            'events' => $events,
            'filters' => $request->only(['search', 'type']),
            'stats' => [
                'total' => Event::count(),
                'upcoming' => Event::where('starts_at', '>=', now())->count(),
                'past' => Event::where('starts_at', '<', now())->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('dashboard/events/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:events',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'location' => 'nullable|string|max:255',
            'type' => 'required|in:powwow,gathering,ceremony',
            'description' => 'nullable|string',
        ]);

        Event::create($validated);

        return to_route('dashboard.events.index')->with('success', 'Event created.');
    }

    public function edit(Event $event): Response
    {
        return Inertia::render('dashboard/events/Edit', [
            'event' => $event,
        ]);
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:events,slug,'.$event->id,
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'location' => 'nullable|string|max:255',
            'type' => 'required|in:powwow,gathering,ceremony',
            'description' => 'nullable|string',
        ]);

        $event->update($validated);

        return to_route('dashboard.events.index')->with('success', 'Event updated.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return to_route('dashboard.events.index')->with('success', 'Event deleted.');
    }
}
```

**Step 4: Create GroupController** (same pattern)

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GroupController extends Controller
{
    public function index(Request $request): Response
    {
        $groups = Group::query()
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('dashboard/groups/Index', [
            'groups' => $groups,
            'filters' => $request->only(['search', 'type']),
            'stats' => [
                'total' => Group::count(),
                'online' => Group::where('type', 'online')->count(),
                'offline' => Group::where('type', 'offline')->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('dashboard/groups/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:groups',
            'type' => 'required|in:online,offline',
            'url' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'region' => 'nullable|string|max:255',
        ]);

        Group::create($validated);

        return to_route('dashboard.groups.index')->with('success', 'Group created.');
    }

    public function edit(Group $group): Response
    {
        return Inertia::render('dashboard/groups/Edit', [
            'group' => $group,
        ]);
    }

    public function update(Request $request, Group $group): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:groups,slug,'.$group->id,
            'type' => 'required|in:online,offline',
            'url' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'region' => 'nullable|string|max:255',
        ]);

        $group->update($validated);

        return to_route('dashboard.groups.index')->with('success', 'Group updated.');
    }

    public function destroy(Group $group): RedirectResponse
    {
        $group->delete();

        return to_route('dashboard.groups.index')->with('success', 'Group deleted.');
    }
}
```

**Step 5: Create TeachingController** (same pattern)

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Teaching;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeachingController extends Controller
{
    public function index(Request $request): Response
    {
        $teachings = Teaching::query()
            ->when($request->search, fn ($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('dashboard/teachings/Index', [
            'teachings' => $teachings,
            'filters' => $request->only(['search', 'type']),
            'stats' => [
                'total' => Teaching::count(),
                'culture' => Teaching::where('type', 'culture')->count(),
                'history' => Teaching::where('type', 'history')->count(),
                'language' => Teaching::where('type', 'language')->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('dashboard/teachings/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:teachings',
            'type' => 'required|in:culture,history,language',
            'content' => 'required|string',
        ]);

        Teaching::create($validated);

        return to_route('dashboard.teachings.index')->with('success', 'Teaching created.');
    }

    public function edit(Teaching $teaching): Response
    {
        return Inertia::render('dashboard/teachings/Edit', [
            'teaching' => $teaching,
        ]);
    }

    public function update(Request $request, Teaching $teaching): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:teachings,slug,'.$teaching->id,
            'type' => 'required|in:culture,history,language',
            'content' => 'required|string',
        ]);

        $teaching->update($validated);

        return to_route('dashboard.teachings.index')->with('success', 'Teaching updated.');
    }

    public function destroy(Teaching $teaching): RedirectResponse
    {
        $teaching->delete();

        return to_route('dashboard.teachings.index')->with('success', 'Teaching deleted.');
    }
}
```

**Step 6: Add routes to web.php**

Add after the dashboard route:

```php
use App\Http\Controllers\Dashboard\EventController;
use App\Http\Controllers\Dashboard\GroupController;
use App\Http\Controllers\Dashboard\TeachingController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('dashboard/events', EventController::class)->names('dashboard.events');
    Route::resource('dashboard/groups', GroupController::class)->names('dashboard.groups');
    Route::resource('dashboard/teachings', TeachingController::class)->names('dashboard.teachings');
});
```

**Step 7: Run tests**

Run: `php artisan test --filter=EventControllerTest`
Expected: PASS

**Step 8: Commit**

```bash
git add app/Http/Controllers/Dashboard/ routes/web.php tests/Feature/Dashboard/
git commit -m "feat: add admin CRUD controllers and routes for events, groups, teachings"
```

---

### Task 6: Add Sidebar Navigation for Admin Sections

**Files:**
- Modify: `resources/js/components/AppSidebar.vue`

**Step 1: Add nav items for events, groups, teachings**

Add to the `mainNavItems` array in `AppSidebar.vue`:

```typescript
import { Calendar, LayoutGrid, Newspaper, Users, BookOpen } from 'lucide-vue-next';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Articles',
        href: '/dashboard/articles',
        icon: Newspaper,
    },
    {
        title: 'Events',
        href: '/dashboard/events',
        icon: Calendar,
    },
    {
        title: 'Groups',
        href: '/dashboard/groups',
        icon: Users,
    },
    {
        title: 'Teachings',
        href: '/dashboard/teachings',
        icon: BookOpen,
    },
];
```

**Step 2: Run lint**

Run: `npm run lint:check`
Expected: PASS

**Step 3: Commit**

```bash
git add resources/js/components/AppSidebar.vue
git commit -m "feat: add sidebar navigation for events, groups, teachings admin"
```

---

### Task 7: Create Admin Vue Pages for Events

**Files:**
- Create: `resources/js/pages/dashboard/events/Index.vue`
- Create: `resources/js/pages/dashboard/events/Create.vue`
- Create: `resources/js/pages/dashboard/events/Edit.vue`

Follow the article admin page patterns: use `AppLayout`, breadcrumbs, same component patterns (`Card`, `Button`, `Input`, `Label`, `Badge`). Each page uses `@inertiajs/vue3` `useForm` for form handling and `router` for navigation.

**Index.vue**: Table of events with title, type badge, date, location. Search filter. Stats cards (total, upcoming, past). Delete with confirmation.

**Create.vue**: Form with fields: title, slug (auto-generated from title), type (select), starts_at (datetime-local), ends_at (datetime-local), location, description (textarea).

**Edit.vue**: Same form pre-filled with event data.

**Step 1: Create the three Vue files**

(Implementation follows existing article admin patterns — uses `useForm`, `router.delete`, same layout/breadcrumb structure)

**Step 2: Run checks**

Run: `npm run type-check && npm run lint:check`
Expected: PASS

**Step 3: Commit**

```bash
git add resources/js/pages/dashboard/events/
git commit -m "feat: add admin Vue pages for event management"
```

---

### Task 8: Create Admin Vue Pages for Groups

**Files:**
- Create: `resources/js/pages/dashboard/groups/Index.vue`
- Create: `resources/js/pages/dashboard/groups/Create.vue`
- Create: `resources/js/pages/dashboard/groups/Edit.vue`

Same pattern as events. Fields: name, slug, type (online/offline), url, description, region.

**Step 1: Create the three Vue files**

**Step 2: Run checks**

Run: `npm run type-check && npm run lint:check`
Expected: PASS

**Step 3: Commit**

```bash
git add resources/js/pages/dashboard/groups/
git commit -m "feat: add admin Vue pages for group management"
```

---

### Task 9: Create Admin Vue Pages for Teachings

**Files:**
- Create: `resources/js/pages/dashboard/teachings/Index.vue`
- Create: `resources/js/pages/dashboard/teachings/Create.vue`
- Create: `resources/js/pages/dashboard/teachings/Edit.vue`

Same pattern. Fields: title, slug, type (culture/history/language), content (textarea).

**Step 1: Create the three Vue files**

**Step 2: Run checks**

Run: `npm run type-check && npm run lint:check`
Expected: PASS

**Step 3: Commit**

```bash
git add resources/js/pages/dashboard/teachings/
git commit -m "feat: add admin Vue pages for teaching management"
```

---

### Task 10: Add HasFactory to Models

**Files:**
- Modify: `app/Models/Event.php`
- Modify: `app/Models/Group.php`
- Modify: `app/Models/Teaching.php`

Add `use Illuminate\Database\Eloquent\Factories\HasFactory;` and the `HasFactory` trait to each model so factories work.

**Step 1: Update models**

**Step 2: Run full test suite**

Run: `composer test && npm run test`
Expected: All PASS

**Step 3: Commit**

```bash
git add app/Models/Event.php app/Models/Group.php app/Models/Teaching.php
git commit -m "feat: add HasFactory trait to Event, Group, Teaching models"
```

---

### Task 11: Final Integration Test & Cleanup

**Step 1: Run full test suite**

Run: `composer test`
Expected: All PASS

**Step 2: Run frontend checks**

Run: `npm run lint:check && npm run format:check && npm run type-check`
Expected: All PASS

**Step 3: Regenerate Wayfinder routes**

Run: `php artisan wayfinder:generate --with-form`

**Step 4: Test locally with seeded data**

Run: `php artisan migrate:fresh --seed && composer dev`
Visit homepage — should show seeded events and groups (articles will be empty locally unless Redis is running).

**Step 5: Final commit and push**

```bash
git push origin main
```
