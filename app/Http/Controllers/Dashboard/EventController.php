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
