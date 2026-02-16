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
