# Phase 1: Cultural Tree + Public Pages — Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Build the cultural group tree data model, dashboard CRUD, public-facing culture/teachings pages, seed Anishinaabe data, and update the site framing from "Anishinaabe" to "Indigenous".

**Architecture:** Self-referencing `cultural_groups` table linked to existing `teachings` via FK. New public controllers serve Inertia pages under `/culture` and `/teachings`. Dashboard gets a new resource controller for cultural group management. Existing teaching forms gain a cultural group selector.

**Tech Stack:** Laravel 12, Pest 4 (TDD), Vue 3 + TypeScript + Inertia.js 2, shadcn-vue, Tailwind CSS 4

**Design Doc:** `docs/plans/2026-02-26-indigenous-cultural-section-design.md`

---

### Task 1: CulturalGroup Migration

**Files:**
- Create: `database/migrations/YYYY_MM_DD_HHMMSS_create_cultural_groups_table.php`

**Step 1: Create migration**

```bash
php artisan make:migration create_cultural_groups_table
```

**Step 2: Write migration schema**

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cultural_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('cultural_groups')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('depth_type'); // root, family, group, sub_group, community, clan
            $table->text('description')->nullable();
            $table->foreignId('media_id')->nullable()->constrained()->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cultural_groups');
    }
};
```

**Step 3: Run migration**

```bash
php artisan migrate
```

Expected: Migration runs successfully, `cultural_groups` table created.

**Step 4: Commit**

```bash
git add database/migrations/*create_cultural_groups_table*
git commit -m "feat: add cultural_groups migration"
```

---

### Task 2: CulturalGroup Model

**Files:**
- Create: `app/Models/CulturalGroup.php`
- Test: `tests/Feature/Models/CulturalGroupTest.php`

**Step 1: Write failing tests**

```php
<?php

use App\Models\CulturalGroup;
use App\Models\Teaching;

test('cultural group has fillable attributes', function () {
    $group = CulturalGroup::factory()->create([
        'name' => 'Anishinaabe',
        'slug' => 'anishinaabe',
        'depth_type' => 'family',
    ]);

    expect($group->name)->toBe('Anishinaabe');
    expect($group->slug)->toBe('anishinaabe');
    expect($group->depth_type)->toBe('family');
});

test('cultural group can have a parent', function () {
    $parent = CulturalGroup::factory()->create(['name' => 'Indigenous', 'depth_type' => 'root']);
    $child = CulturalGroup::factory()->create(['parent_id' => $parent->id, 'depth_type' => 'family']);

    expect($child->parent->id)->toBe($parent->id);
});

test('cultural group can have children', function () {
    $parent = CulturalGroup::factory()->create(['depth_type' => 'root']);
    CulturalGroup::factory(3)->create(['parent_id' => $parent->id, 'depth_type' => 'family']);

    expect($parent->children)->toHaveCount(3);
});

test('cultural group can have teachings', function () {
    $group = CulturalGroup::factory()->create();
    Teaching::factory(2)->create(['cultural_group_id' => $group->id]);

    expect($group->teachings)->toHaveCount(2);
});

test('cultural group ancestors returns ancestor chain', function () {
    $root = CulturalGroup::factory()->create(['name' => 'Indigenous', 'depth_type' => 'root']);
    $family = CulturalGroup::factory()->create(['name' => 'Anishinaabe', 'parent_id' => $root->id, 'depth_type' => 'family']);
    $group = CulturalGroup::factory()->create(['name' => 'Ojibwe', 'parent_id' => $family->id, 'depth_type' => 'group']);

    $ancestors = $group->ancestors();

    expect($ancestors)->toHaveCount(2);
    expect($ancestors[0]->name)->toBe('Indigenous');
    expect($ancestors[1]->name)->toBe('Anishinaabe');
});
```

**Step 2: Run tests to verify they fail**

```bash
php artisan test --filter=CulturalGroupTest
```

Expected: FAIL — `CulturalGroup` class not found.

**Step 3: Create the model**

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CulturalGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'depth_type',
        'description',
        'media_id',
        'metadata',
        'sort_order',
    ];

    protected $casts = [
        'metadata' => 'array',
        'sort_order' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CulturalGroup::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CulturalGroup::class, 'parent_id')->orderBy('sort_order');
    }

    public function teachings(): HasMany
    {
        return $this->hasMany(Teaching::class);
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /**
     * Get all ancestors from root to direct parent.
     *
     * @return Collection<int, CulturalGroup>
     */
    public function ancestors(): Collection
    {
        $ancestors = collect();
        $current = $this->parent;

        while ($current) {
            $ancestors->prepend($current);
            $current = $current->parent;
        }

        return new Collection($ancestors->all());
    }
}
```

**Step 4: Create the factory**

Create `database/factories/CulturalGroupFactory.php`:

```php
<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CulturalGroup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<CulturalGroup> */
class CulturalGroupFactory extends Factory
{
    protected $model = CulturalGroup::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'depth_type' => fake()->randomElement(['root', 'family', 'group', 'sub_group', 'community', 'clan']),
            'description' => fake()->optional()->sentence(),
            'sort_order' => 0,
        ];
    }
}
```

**Step 5: Run tests to verify they pass**

```bash
php artisan test --filter=CulturalGroupTest
```

Expected: Tests fail on `cultural_group_id` column not existing in teachings (Task 3 fixes this). The first 3 tests (fillable, parent, children) should pass. Skip to Task 3 then return.

**Step 6: Commit**

```bash
git add app/Models/CulturalGroup.php database/factories/CulturalGroupFactory.php tests/Feature/Models/CulturalGroupTest.php
git commit -m "feat: add CulturalGroup model, factory, and tests"
```

---

### Task 3: Add cultural_group_id to Teachings

**Files:**
- Create: `database/migrations/YYYY_MM_DD_HHMMSS_add_cultural_group_id_to_teachings_table.php`
- Modify: `app/Models/Teaching.php`

**Step 1: Create migration**

```bash
php artisan make:migration add_cultural_group_id_to_teachings_table
```

**Step 2: Write migration**

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachings', function (Blueprint $table) {
            $table->foreignId('cultural_group_id')->nullable()->after('media_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('teachings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cultural_group_id');
        });
    }
};
```

**Step 3: Run migration**

```bash
php artisan migrate
```

**Step 4: Update Teaching model — add relationship and fillable**

In `app/Models/Teaching.php`, add `'cultural_group_id'` to `$fillable` and add:

```php
public function culturalGroup(): BelongsTo
{
    return $this->belongsTo(CulturalGroup::class);
}
```

**Step 5: Run all CulturalGroup tests**

```bash
php artisan test --filter=CulturalGroupTest
```

Expected: All 5 tests PASS.

**Step 6: Run existing teaching tests to verify no regressions**

```bash
php artisan test --filter=TeachingController
```

Expected: All existing teaching tests PASS (cultural_group_id is nullable, so no breaking changes).

**Step 7: Commit**

```bash
git add database/migrations/*add_cultural_group_id_to_teachings* app/Models/Teaching.php
git commit -m "feat: add cultural_group_id FK to teachings table"
```

---

### Task 4: CulturalGroup Seeder

**Files:**
- Create: `database/seeders/CulturalGroupSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`

**Step 1: Create seeder**

```bash
php artisan make:seeder CulturalGroupSeeder
```

**Step 2: Write seeder with Anishinaabe tree**

```php
<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CulturalGroup;
use Illuminate\Database\Seeder;

class CulturalGroupSeeder extends Seeder
{
    public function run(): void
    {
        $root = CulturalGroup::create([
            'name' => 'Indigenous',
            'slug' => 'indigenous',
            'depth_type' => 'root',
            'description' => 'Indigenous peoples of Turtle Island and beyond.',
            'sort_order' => 0,
        ]);

        $anishinaabe = CulturalGroup::create([
            'name' => 'Anishinaabe',
            'slug' => 'anishinaabe',
            'depth_type' => 'family',
            'parent_id' => $root->id,
            'description' => 'The Anishinaabe are a group of culturally related Indigenous peoples in the Great Lakes region of Canada and the United States.',
            'sort_order' => 0,
        ]);

        $groups = [
            ['name' => 'Ojibwe', 'slug' => 'ojibwe', 'description' => 'Also known as Chippewa, one of the largest Indigenous groups in North America.', 'sort_order' => 0],
            ['name' => 'Odawa', 'slug' => 'odawa', 'description' => 'The Odawa (Ottawa) people, part of the Council of Three Fires.', 'sort_order' => 1],
            ['name' => 'Potawatomi', 'slug' => 'potawatomi', 'description' => 'The Potawatomi, Keepers of the Fire in the Council of Three Fires.', 'sort_order' => 2],
        ];

        foreach ($groups as $group) {
            CulturalGroup::create(array_merge($group, [
                'depth_type' => 'group',
                'parent_id' => $anishinaabe->id,
            ]));
        }
    }
}
```

**Step 3: Update DatabaseSeeder**

Add `$this->call(CulturalGroupSeeder::class);` to `DatabaseSeeder::run()` before Teaching factory calls.

**Step 4: Test seeder**

```bash
php artisan db:seed --class=CulturalGroupSeeder
```

Expected: 5 cultural groups created (Indigenous > Anishinaabe > Ojibwe, Odawa, Potawatomi).

**Step 5: Commit**

```bash
git add database/seeders/CulturalGroupSeeder.php database/seeders/DatabaseSeeder.php
git commit -m "feat: add CulturalGroup seeder with Anishinaabe tree"
```

---

### Task 5: Dashboard CulturalGroupController (Backend)

**Files:**
- Create: `app/Http/Controllers/Dashboard/CulturalGroupController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Dashboard/CulturalGroupControllerTest.php`

**Step 1: Write failing tests**

```php
<?php

use App\Models\CulturalGroup;
use App\Models\User;

test('cultural group index requires authentication', function () {
    $this->get('/dashboard/cultural-groups')->assertRedirect('/login');
});

test('cultural group index returns cultural groups', function () {
    $user = User::factory()->create();
    CulturalGroup::factory(3)->create();

    $this->withoutVite()
        ->actingAs($user)
        ->get('/dashboard/cultural-groups')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/cultural-groups/Index')
            ->has('culturalGroups.data', 3)
        );
});

test('cultural group can be created', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/dashboard/cultural-groups', [
            'name' => 'Haudenosaunee',
            'slug' => 'haudenosaunee',
            'depth_type' => 'family',
            'description' => 'People of the Longhouse.',
        ])
        ->assertRedirect('/dashboard/cultural-groups');

    expect(CulturalGroup::where('name', 'Haudenosaunee')->exists())->toBeTrue();
});

test('cultural group can be created with parent', function () {
    $user = User::factory()->create();
    $parent = CulturalGroup::factory()->create(['depth_type' => 'root']);

    $this->actingAs($user)
        ->post('/dashboard/cultural-groups', [
            'name' => 'Anishinaabe',
            'slug' => 'anishinaabe',
            'depth_type' => 'family',
            'parent_id' => $parent->id,
        ])
        ->assertRedirect('/dashboard/cultural-groups');

    $child = CulturalGroup::where('name', 'Anishinaabe')->first();
    expect($child->parent_id)->toBe($parent->id);
});

test('cultural group can be updated', function () {
    $user = User::factory()->create();
    $group = CulturalGroup::factory()->create();

    $this->actingAs($user)
        ->put("/dashboard/cultural-groups/{$group->id}", [
            'name' => 'Updated Name',
            'slug' => $group->slug,
            'depth_type' => $group->depth_type,
        ])
        ->assertRedirect('/dashboard/cultural-groups');

    expect($group->fresh()->name)->toBe('Updated Name');
});

test('cultural group can be deleted', function () {
    $user = User::factory()->create();
    $group = CulturalGroup::factory()->create();

    $this->actingAs($user)
        ->delete("/dashboard/cultural-groups/{$group->id}")
        ->assertRedirect('/dashboard/cultural-groups');

    expect(CulturalGroup::find($group->id))->toBeNull();
});
```

**Step 2: Run tests to verify they fail**

```bash
php artisan test --filter=CulturalGroupControllerTest
```

Expected: FAIL — route not defined.

**Step 3: Create the controller**

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CulturalGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CulturalGroupController extends Controller
{
    public function index(Request $request): Response
    {
        $culturalGroups = CulturalGroup::query()
            ->with('parent')
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->depth_type, fn ($q) => $q->where('depth_type', $request->depth_type))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('dashboard/cultural-groups/Index', [
            'culturalGroups' => $culturalGroups,
            'filters' => $request->only(['search', 'depth_type']),
            'stats' => [
                'total' => CulturalGroup::count(),
                'root' => CulturalGroup::where('depth_type', 'root')->count(),
                'family' => CulturalGroup::where('depth_type', 'family')->count(),
                'group' => CulturalGroup::where('depth_type', 'group')->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('dashboard/cultural-groups/Create', [
            'parentOptions' => CulturalGroup::select('id', 'name', 'depth_type')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:cultural_groups',
            'depth_type' => 'required|in:root,family,group,sub_group,community,clan',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:cultural_groups,id',
            'sort_order' => 'nullable|integer',
        ]);

        CulturalGroup::create($validated);

        return to_route('dashboard.cultural-groups.index')->with('success', 'Cultural group created.');
    }

    public function edit(CulturalGroup $culturalGroup): Response
    {
        return Inertia::render('dashboard/cultural-groups/Edit', [
            'culturalGroup' => $culturalGroup->load('parent'),
            'parentOptions' => CulturalGroup::where('id', '!=', $culturalGroup->id)
                ->select('id', 'name', 'depth_type')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(Request $request, CulturalGroup $culturalGroup): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:cultural_groups,slug,' . $culturalGroup->id,
            'depth_type' => 'required|in:root,family,group,sub_group,community,clan',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:cultural_groups,id',
            'sort_order' => 'nullable|integer',
        ]);

        $culturalGroup->update($validated);

        return to_route('dashboard.cultural-groups.index')->with('success', 'Cultural group updated.');
    }

    public function destroy(CulturalGroup $culturalGroup): RedirectResponse
    {
        $culturalGroup->delete();

        return to_route('dashboard.cultural-groups.index')->with('success', 'Cultural group deleted.');
    }
}
```

**Step 4: Register route in `routes/web.php`**

Add inside the `auth` + `verified` middleware group:

```php
use App\Http\Controllers\Dashboard\CulturalGroupController;

Route::resource('dashboard/cultural-groups', CulturalGroupController::class)->names('dashboard.cultural-groups');
```

**Step 5: Run tests**

```bash
php artisan test --filter=CulturalGroupControllerTest
```

Expected: Tests that don't need Vue pages will pass (create, update, delete). Index test will fail until Vue page exists — that's OK, the redirect assertions should pass.

**Step 6: Commit**

```bash
git add app/Http/Controllers/Dashboard/CulturalGroupController.php tests/Feature/Dashboard/CulturalGroupControllerTest.php routes/web.php
git commit -m "feat: add CulturalGroupController with CRUD and tests"
```

---

### Task 6: Dashboard Cultural Group Vue Pages

**Files:**
- Create: `resources/js/pages/dashboard/cultural-groups/Index.vue`
- Create: `resources/js/pages/dashboard/cultural-groups/Create.vue`
- Create: `resources/js/pages/dashboard/cultural-groups/Edit.vue`
- Modify: `resources/js/components/AppSidebar.vue`

**Step 1: Create Index page**

Model after existing `dashboard/teachings/Index.vue`. Key differences:
- Display columns: Name, Depth Type, Parent, Description
- Stats: Total, Root, Family, Group
- Filter by `depth_type` instead of `type`
- Show parent name in table rows

Use the same components: `StatCard`, `DeleteConfirmDialog`, `Badge`, `Button`, `Input`, `AppLayout`.

Icons: Use `TreePine` from lucide-vue-next for cultural groups.

**Step 2: Create Create page**

Model after `dashboard/teachings/Create.vue`. Key differences:
- Fields: name, slug (auto-generated from name), depth_type (select), parent_id (select from `parentOptions` prop), description (textarea)
- `depth_type` select options: root, family, group, sub_group, community, clan
- `parent_id` select populated from `parentOptions` prop passed by controller

**Step 3: Create Edit page**

Model after `dashboard/teachings/Edit.vue`. Same fields as Create, pre-populated from `culturalGroup` prop.

**Step 4: Add to dashboard sidebar**

In `resources/js/components/AppSidebar.vue`, add to `mainNavItems` array:

```typescript
{
    title: 'Cultural Groups',
    href: '/dashboard/cultural-groups',
    icon: TreePine,
},
```

Import `TreePine` from `lucide-vue-next`.

**Step 5: Run all tests**

```bash
php artisan test --filter=CulturalGroupControllerTest
```

Expected: All 6 tests PASS (including index which now has the Vue component).

**Step 6: Commit**

```bash
git add resources/js/pages/dashboard/cultural-groups/ resources/js/components/AppSidebar.vue
git commit -m "feat: add dashboard cultural group pages and sidebar nav"
```

---

### Task 7: Update Teaching Dashboard for Cultural Groups

**Files:**
- Modify: `app/Http/Controllers/Dashboard/TeachingController.php`
- Modify: `resources/js/pages/dashboard/teachings/Create.vue`
- Modify: `resources/js/pages/dashboard/teachings/Edit.vue`
- Modify: `resources/js/pages/dashboard/teachings/Index.vue`
- Modify: `tests/Feature/Dashboard/TeachingControllerTest.php`

**Step 1: Write test for teaching with cultural group**

Add to `TeachingControllerTest.php`:

```php
test('teaching can be created with cultural group', function () {
    $user = User::factory()->create();
    $group = \App\Models\CulturalGroup::factory()->create();

    $this->actingAs($user)
        ->post('/dashboard/teachings', [
            'title' => 'Seven Grandfather Teachings',
            'slug' => 'seven-grandfather-teachings-new',
            'type' => 'culture',
            'content' => 'The Seven Grandfather Teachings.',
            'cultural_group_id' => $group->id,
        ])
        ->assertRedirect('/dashboard/teachings');

    $teaching = Teaching::where('slug', 'seven-grandfather-teachings-new')->first();
    expect($teaching->cultural_group_id)->toBe($group->id);
});
```

**Step 2: Run test to verify it fails**

```bash
php artisan test --filter="teaching can be created with cultural group"
```

Expected: FAIL — `cultural_group_id` not in validation rules.

**Step 3: Update TeachingController**

In `store()` and `update()` validation, add:

```php
'cultural_group_id' => 'nullable|exists:cultural_groups,id',
```

In `create()` and `edit()`, pass cultural group options:

```php
'culturalGroups' => CulturalGroup::select('id', 'name')->orderBy('name')->get(),
```

In `index()`, eager load the cultural group:

```php
->with('culturalGroup:id,name')
```

**Step 4: Update Create.vue**

Add `cultural_group_id` to the form, add a Select dropdown populated from `culturalGroups` prop. Place it after the type selector.

**Step 5: Update Edit.vue**

Same as Create — add cultural group selector, pre-populated from `teaching.cultural_group_id`.

**Step 6: Update Index.vue**

Add "Cultural Group" column to the table showing `teaching.cultural_group?.name`.

**Step 7: Run all teaching tests**

```bash
php artisan test --filter=TeachingController
```

Expected: All tests PASS (5 tests, including the new one).

**Step 8: Commit**

```bash
git add app/Http/Controllers/Dashboard/TeachingController.php resources/js/pages/dashboard/teachings/ tests/Feature/Dashboard/TeachingControllerTest.php
git commit -m "feat: add cultural group selector to teaching dashboard"
```

---

### Task 8: Public CultureController (Backend)

**Files:**
- Create: `app/Http/Controllers/CultureController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/CultureControllerTest.php`

**Step 1: Write failing tests**

```php
<?php

use App\Models\CulturalGroup;
use App\Models\Teaching;

test('culture index shows root cultural groups', function () {
    $root = CulturalGroup::factory()->create(['depth_type' => 'root']);
    CulturalGroup::factory(2)->create(['parent_id' => $root->id, 'depth_type' => 'family']);

    $this->withoutVite()
        ->get('/culture')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('culture/Index')
            ->has('groups', 1)
            ->where('groups.0.children_count', 2)
        );
});

test('culture show displays a cultural group with children and teachings', function () {
    $group = CulturalGroup::factory()->create(['slug' => 'anishinaabe', 'depth_type' => 'family']);
    CulturalGroup::factory(3)->create(['parent_id' => $group->id, 'depth_type' => 'group']);
    Teaching::factory(2)->create(['cultural_group_id' => $group->id]);

    $this->withoutVite()
        ->get('/culture/anishinaabe')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('culture/Show')
            ->where('group.slug', 'anishinaabe')
            ->has('children', 3)
            ->has('teachings.data', 2)
            ->has('breadcrumb')
        );
});

test('culture show returns 404 for nonexistent slug', function () {
    $this->get('/culture/nonexistent')->assertNotFound();
});
```

**Step 2: Run tests to verify they fail**

```bash
php artisan test --filter=CultureControllerTest
```

Expected: FAIL — route not defined.

**Step 3: Create the controller**

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CulturalGroup;
use Inertia\Inertia;
use Inertia\Response;

class CultureController extends Controller
{
    public function index(): Response
    {
        $groups = CulturalGroup::whereNull('parent_id')
            ->withCount('children')
            ->with('children:id,parent_id,name,slug,depth_type')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('culture/Index', [
            'groups' => $groups,
        ]);
    }

    public function show(string $slug): Response
    {
        $group = CulturalGroup::where('slug', $slug)->firstOrFail();

        $children = $group->children()
            ->withCount('children', 'teachings')
            ->get();

        $teachings = $group->teachings()
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        $breadcrumb = $group->ancestors()->push($group)->map(fn ($g) => [
            'name' => $g->name,
            'slug' => $g->slug,
        ])->values()->all();

        return Inertia::render('culture/Show', [
            'group' => $group,
            'children' => $children,
            'teachings' => $teachings,
            'breadcrumb' => $breadcrumb,
        ]);
    }
}
```

**Step 4: Register routes in `routes/web.php`**

Add as public routes (no auth):

```php
use App\Http\Controllers\CultureController;

Route::get('/culture', [CultureController::class, 'index'])->name('culture.index');
Route::get('/culture/{slug}', [CultureController::class, 'show'])->name('culture.show');
```

**Step 5: Run tests**

```bash
php artisan test --filter=CultureControllerTest
```

Expected: Tests pass once Vue pages exist. Index/show tests may fail until Vue pages are created — the redirect/404 test should pass.

**Step 6: Commit**

```bash
git add app/Http/Controllers/CultureController.php tests/Feature/CultureControllerTest.php routes/web.php
git commit -m "feat: add public CultureController with index and show"
```

---

### Task 9: Public Culture Vue Pages

**Files:**
- Create: `resources/js/pages/culture/Index.vue`
- Create: `resources/js/pages/culture/Show.vue`

**Step 1: Create culture/Index.vue — landing page**

Uses `PublicLayout`. Displays the root cultural groups as cards, each showing name, description, child count. Cards link to `/culture/{slug}`.

Key elements:
- Page title: "Indigenous Culture"
- Grid of cultural group cards
- Each card shows: name, description, number of child groups
- Cards link to `/culture/{slug}` via Inertia `Link`

Use `Card`, `CardHeader`, `CardTitle`, `CardDescription` from shadcn-vue. Follow existing Home/Index.vue patterns.

**Step 2: Create culture/Show.vue — cultural group detail page**

Uses `PublicLayout`. Shows:
- Breadcrumb navigation (Indigenous > Anishinaabe > Ojibwe)
- Group name and description
- Children grid (if any) — cards linking to child groups
- Teachings grid — cards for each teaching, linking to `/teachings/{slug}`

Key elements:
- Breadcrumb from `breadcrumb` prop using `Link` components
- Two sections: "Sub-groups" (children) and "Teachings"
- Pagination for teachings

**Step 3: Run CultureController tests**

```bash
php artisan test --filter=CultureControllerTest
```

Expected: All 3 tests PASS.

**Step 4: Commit**

```bash
git add resources/js/pages/culture/
git commit -m "feat: add public culture landing and detail pages"
```

---

### Task 10: Public TeachingController (Backend)

**Files:**
- Create: `app/Http/Controllers/TeachingController.php` (public, separate from dashboard)
- Modify: `routes/web.php`
- Test: `tests/Feature/TeachingControllerTest.php`

Note: The public controller is in `App\Http\Controllers`, NOT in `Dashboard` namespace.

**Step 1: Write failing tests**

```php
<?php

use App\Models\CulturalGroup;
use App\Models\Teaching;

test('teachings index shows all teachings', function () {
    Teaching::factory(5)->create();

    $this->withoutVite()
        ->get('/teachings')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('teachings/Index')
            ->has('teachings.data', 5)
        );
});

test('teachings index can filter by type', function () {
    Teaching::factory(3)->create(['type' => 'culture']);
    Teaching::factory(2)->create(['type' => 'history']);

    $this->withoutVite()
        ->get('/teachings?type=culture')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('teachings.data', 3)
        );
});

test('teaching show displays a single teaching', function () {
    $group = CulturalGroup::factory()->create();
    $teaching = Teaching::factory()->create([
        'slug' => 'seven-grandfather-teachings',
        'cultural_group_id' => $group->id,
    ]);

    $this->withoutVite()
        ->get('/teachings/seven-grandfather-teachings')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('teachings/Show')
            ->where('teaching.slug', 'seven-grandfather-teachings')
            ->has('culturalGroup')
        );
});

test('teaching show returns 404 for nonexistent slug', function () {
    $this->get('/teachings/nonexistent')->assertNotFound();
});
```

**Step 2: Run tests to verify they fail**

```bash
php artisan test tests/Feature/TeachingControllerTest.php
```

Expected: FAIL — route not defined (or conflicts with dashboard).

**Step 3: Create public teaching controller**

Name it `PublicTeachingController` to avoid conflict with the dashboard one:

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Teaching;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicTeachingController extends Controller
{
    public function index(Request $request): Response
    {
        $teachings = Teaching::query()
            ->with('culturalGroup:id,name,slug')
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->when($request->search, fn ($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('teachings/Index', [
            'teachings' => $teachings,
            'filters' => $request->only(['type', 'search']),
        ]);
    }

    public function show(string $slug): Response
    {
        $teaching = Teaching::where('slug', $slug)
            ->with('culturalGroup:id,name,slug')
            ->firstOrFail();

        return Inertia::render('teachings/Show', [
            'teaching' => $teaching,
            'culturalGroup' => $teaching->culturalGroup,
        ]);
    }
}
```

**Step 4: Register routes**

In `routes/web.php`, add as public routes:

```php
use App\Http\Controllers\PublicTeachingController;

Route::get('/teachings', [PublicTeachingController::class, 'index'])->name('teachings.index');
Route::get('/teachings/{slug}', [PublicTeachingController::class, 'show'])->name('teachings.show');
```

**Step 5: Run tests**

```bash
php artisan test tests/Feature/TeachingControllerTest.php
```

Expected: Tests pass once Vue pages exist. The 404 test should pass immediately.

**Step 6: Commit**

```bash
git add app/Http/Controllers/PublicTeachingController.php tests/Feature/TeachingControllerTest.php routes/web.php
git commit -m "feat: add public teaching controller with index and show"
```

---

### Task 11: Public Teaching Vue Pages

**Files:**
- Create: `resources/js/pages/teachings/Index.vue`
- Create: `resources/js/pages/teachings/Show.vue`

**Step 1: Create teachings/Index.vue — all teachings page**

Uses `PublicLayout`. Displays all teachings in a filterable grid.

Key elements:
- Page title: "Teachings"
- Filter pills for type: All, Culture, History, Language
- Search input
- Grid of teaching cards showing: title, type badge, cultural group name (linked), content excerpt
- Pagination
- Each card links to `/teachings/{slug}`

**Step 2: Create teachings/Show.vue — single teaching page**

Uses `PublicLayout`. Shows the full teaching content.

Key elements:
- Breadcrumb: Teachings > [Cultural Group] > [Teaching Title]
- Teaching title, type badge
- Cultural group link (if associated)
- Full content rendered
- Back link to teachings index

**Step 3: Run public teaching tests**

```bash
php artisan test tests/Feature/TeachingControllerTest.php
```

Expected: All 4 tests PASS.

**Step 4: Commit**

```bash
git add resources/js/pages/teachings/
git commit -m "feat: add public teaching index and show pages"
```

---

### Task 12: Update Homepage and Layout Framing

**Files:**
- Modify: `resources/js/pages/Home/Index.vue`
- Modify: `resources/js/layouts/PublicLayout.vue`

**Step 1: Update Home/Index.vue**

Changes to make:
- Page title: `"Diidjaaheer — Indigenous News, Culture & Community"` (was "Anishinaabe")
- Hero text line 1: `"Indigenous"` (was "Anishinaabe")
- Hero subtitle: `"Your gathering place for Indigenous news, powwow events, cultural teachings, and community connections across Turtle Island."`
- Teaching cards descriptions: Remove "Anishinaabe" specificity, make inclusive:
  - Culture: `"Traditions, ceremonies, and ways of life passed down through generations."`
  - History: `"The rich histories of Indigenous peoples across Turtle Island."`
  - Language: `"Language resources and revitalization efforts."`
- Community section subtitle: `"Online and offline Indigenous community organizations and groups."` (was "Anishinaabe")
- Add a "Culture" pill in the hero pills linking to `/culture`
- Update nav links in PublicLayout to include Culture link

**Step 2: Update PublicLayout.vue**

Changes to make:
- Footer tagline: `"Indigenous news, culture & community"` (was "North American Anishinaabe...")
- Add `Culture` to `navLinks`: `{ label: 'Culture', href: '/culture' }`
- Add `Culture` to `footerExplore`: `{ label: 'Culture', href: '/culture' }`

**Step 3: Run all tests to check for regressions**

```bash
composer test
```

Expected: All tests pass.

**Step 4: Commit**

```bash
git add resources/js/pages/Home/Index.vue resources/js/layouts/PublicLayout.vue
git commit -m "feat: update homepage and layout framing from Anishinaabe to Indigenous"
```

---

### Task 13: Regenerate Wayfinder Routes and Final Lint

**Step 1: Regenerate Wayfinder**

```bash
php artisan wayfinder:generate --with-form
```

**Step 2: Run full lint and format**

```bash
composer lint && npm run lint && npm run format
```

**Step 3: Run full test suite**

```bash
composer test && npm run test
```

Expected: All backend and frontend tests pass, no lint errors.

**Step 4: Run type check**

```bash
npm run type-check
```

Expected: No TypeScript errors.

**Step 5: Commit any lint fixes**

```bash
git add -A
git commit -m "style: lint and format fixes"
```

---

## Summary

| Task | What | Files Created/Modified |
|------|------|-----------------------|
| 1 | Cultural groups migration | 1 migration |
| 2 | CulturalGroup model + factory + tests | 3 files |
| 3 | Add cultural_group_id to teachings | 1 migration, 1 model |
| 4 | Seeder with Anishinaabe data | 2 seeders |
| 5 | Dashboard CulturalGroupController + tests | 2 files + route |
| 6 | Dashboard cultural group Vue pages + sidebar | 3 Vue pages + sidebar |
| 7 | Update teaching dashboard for cultural groups | 3 Vue pages + controller + test |
| 8 | Public CultureController + tests | 2 files + route |
| 9 | Public culture Vue pages | 2 Vue pages |
| 10 | Public TeachingController + tests | 2 files + route |
| 11 | Public teaching Vue pages | 2 Vue pages |
| 12 | Update homepage/layout framing | 2 Vue files |
| 13 | Wayfinder + lint + full test | No new files |
