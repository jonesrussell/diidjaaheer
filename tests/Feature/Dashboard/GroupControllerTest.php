<?php

use App\Models\Group;
use App\Models\User;

test('group index requires authentication', function () {
    $this->get('/dashboard/groups')->assertRedirect('/login');
});

test('group index returns groups', function () {
    $user = User::factory()->create();
    Group::factory(3)->create();

    $this->withoutVite()
        ->actingAs($user)
        ->get('/dashboard/groups')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/groups/Index')
            ->has('groups.data', 3)
        );
});

test('group can be created', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/dashboard/groups', [
            'name' => 'Ojibwe Language Circle',
            'slug' => 'ojibwe-language-circle',
            'type' => 'online',
            'url' => 'https://example.com/ojibwe',
            'region' => 'Great Lakes',
        ])
        ->assertRedirect('/dashboard/groups');

    expect(Group::where('name', 'Ojibwe Language Circle')->exists())->toBeTrue();
});

test('group can be updated', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create();

    $this->actingAs($user)
        ->put("/dashboard/groups/{$group->id}", [
            'name' => 'Updated Circle',
            'slug' => $group->slug,
            'type' => 'offline',
            'region' => 'Ontario',
        ])
        ->assertRedirect('/dashboard/groups');

    expect($group->fresh()->name)->toBe('Updated Circle');
});

test('group can be deleted', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create();

    $this->actingAs($user)
        ->delete("/dashboard/groups/{$group->id}")
        ->assertRedirect('/dashboard/groups');

    expect(Group::find($group->id))->toBeNull();
});
