<?php

use App\Models\CulturalGroup;
use App\Models\Teaching;

test('cultural group has fillable attributes', function () {
    $group = CulturalGroup::factory()->create([
        'name' => 'Anishinaabe',
        'slug' => 'anishinaabe',
        'depth_type' => 'root',
        'description' => 'The Anishinaabe people.',
        'sort_order' => 1,
    ]);

    expect($group->name)->toBe('Anishinaabe')
        ->and($group->slug)->toBe('anishinaabe')
        ->and($group->depth_type)->toBe('root')
        ->and($group->description)->toBe('The Anishinaabe people.')
        ->and($group->sort_order)->toBe(1);
});

test('cultural group can have a parent', function () {
    $parent = CulturalGroup::factory()->create(['depth_type' => 'root']);
    $child = CulturalGroup::factory()->create([
        'parent_id' => $parent->id,
        'depth_type' => 'family',
    ]);

    expect($child->parent->id)->toBe($parent->id)
        ->and($child->parent)->toBeInstanceOf(CulturalGroup::class);
});

test('cultural group can have children', function () {
    $parent = CulturalGroup::factory()->create(['depth_type' => 'root']);
    $childA = CulturalGroup::factory()->create([
        'parent_id' => $parent->id,
        'depth_type' => 'family',
        'sort_order' => 1,
    ]);
    $childB = CulturalGroup::factory()->create([
        'parent_id' => $parent->id,
        'depth_type' => 'family',
        'sort_order' => 0,
    ]);

    $children = $parent->children;

    expect($children)->toHaveCount(2)
        ->and($children->first()->id)->toBe($childB->id, 'Children should be ordered by sort_order');
});

test('cultural group can have teachings', function () {
    $group = CulturalGroup::factory()->create();
    Teaching::factory(2)->create(['cultural_group_id' => $group->id]);

    expect($group->teachings)->toHaveCount(2)
        ->and($group->teachings->first())->toBeInstanceOf(Teaching::class);
});

test('cultural group ancestors returns ancestor chain root first', function () {
    $root = CulturalGroup::factory()->create([
        'name' => 'Root',
        'depth_type' => 'root',
    ]);
    $family = CulturalGroup::factory()->create([
        'name' => 'Family',
        'parent_id' => $root->id,
        'depth_type' => 'family',
    ]);
    $group = CulturalGroup::factory()->create([
        'name' => 'Group',
        'parent_id' => $family->id,
        'depth_type' => 'group',
    ]);

    $ancestors = $group->ancestors();

    expect($ancestors)->toHaveCount(2)
        ->and($ancestors->first()->name)->toBe('Root')
        ->and($ancestors->last()->name)->toBe('Family');
});
