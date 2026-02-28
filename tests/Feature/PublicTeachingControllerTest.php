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
