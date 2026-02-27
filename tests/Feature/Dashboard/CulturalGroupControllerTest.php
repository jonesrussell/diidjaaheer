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
