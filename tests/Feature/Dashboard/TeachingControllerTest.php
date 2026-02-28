<?php

use App\Models\CulturalGroup;
use App\Models\Teaching;
use App\Models\User;

test('teaching index requires authentication', function () {
    $this->get('/dashboard/teachings')->assertRedirect('/login');
});

test('teaching index returns teachings', function () {
    $user = User::factory()->create();
    Teaching::factory(3)->create();

    $this->withoutVite()
        ->actingAs($user)
        ->get('/dashboard/teachings')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/teachings/Index')
            ->has('teachings.data', 3)
        );
});

test('teaching can be created', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/dashboard/teachings', [
            'title' => 'Seven Grandfather Teachings',
            'slug' => 'seven-grandfather-teachings',
            'type' => 'culture',
            'content' => 'The Seven Grandfather Teachings are a set of teachings on human conduct.',
        ])
        ->assertRedirect('/dashboard/teachings');

    expect(Teaching::where('title', 'Seven Grandfather Teachings')->exists())->toBeTrue();
});

test('teaching can be updated', function () {
    $user = User::factory()->create();
    $teaching = Teaching::factory()->create();

    $this->actingAs($user)
        ->put("/dashboard/teachings/{$teaching->id}", [
            'title' => 'Updated Teaching',
            'slug' => $teaching->slug,
            'type' => 'history',
            'content' => 'Updated content for this teaching.',
        ])
        ->assertRedirect('/dashboard/teachings');

    expect($teaching->fresh()->title)->toBe('Updated Teaching');
});

test('teaching can be created with cultural group', function () {
    $user = User::factory()->create();
    $group = CulturalGroup::factory()->create();

    $this->actingAs($user)
        ->post('/dashboard/teachings', [
            'title' => 'Seven Grandfather Teachings',
            'slug' => 'seven-grandfather-teachings-cg',
            'type' => 'culture',
            'content' => 'The Seven Grandfather Teachings.',
            'cultural_group_id' => $group->id,
        ])
        ->assertRedirect('/dashboard/teachings');

    $teaching = Teaching::where('slug', 'seven-grandfather-teachings-cg')->first();
    expect($teaching->cultural_group_id)->toBe($group->id);
});

test('teaching can be deleted', function () {
    $user = User::factory()->create();
    $teaching = Teaching::factory()->create();

    $this->actingAs($user)
        ->delete("/dashboard/teachings/{$teaching->id}")
        ->assertRedirect('/dashboard/teachings');

    expect(Teaching::find($teaching->id))->toBeNull();
});
