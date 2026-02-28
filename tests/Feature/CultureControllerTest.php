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
