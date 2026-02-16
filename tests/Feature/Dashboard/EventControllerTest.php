<?php

use App\Models\Event;
use App\Models\User;

test('event index requires authentication', function () {
    $this->get('/dashboard/events')->assertRedirect('/login');
});

test('event index returns events', function () {
    $user = User::factory()->create();
    Event::factory(3)->create();

    $this->withoutVite()
        ->actingAs($user)
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
