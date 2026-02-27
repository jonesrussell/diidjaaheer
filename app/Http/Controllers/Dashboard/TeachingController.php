<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CulturalGroup;
use App\Models\Teaching;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeachingController extends Controller
{
    public function index(Request $request): Response
    {
        $teachings = Teaching::query()
            ->with('culturalGroup:id,name')
            ->when($request->search, fn ($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('dashboard/teachings/Index', [
            'teachings' => $teachings,
            'filters' => $request->only(['search', 'type']),
            'stats' => [
                'total' => Teaching::count(),
                'culture' => Teaching::where('type', 'culture')->count(),
                'history' => Teaching::where('type', 'history')->count(),
                'language' => Teaching::where('type', 'language')->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('dashboard/teachings/Create', [
            'culturalGroups' => CulturalGroup::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:teachings',
            'type' => 'required|in:culture,history,language',
            'content' => 'required|string',
            'cultural_group_id' => 'nullable|exists:cultural_groups,id',
        ]);

        Teaching::create($validated);

        return to_route('dashboard.teachings.index')->with('success', 'Teaching created.');
    }

    public function edit(Teaching $teaching): Response
    {
        return Inertia::render('dashboard/teachings/Edit', [
            'teaching' => $teaching,
            'culturalGroups' => CulturalGroup::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Teaching $teaching): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:teachings,slug,'.$teaching->id,
            'type' => 'required|in:culture,history,language',
            'content' => 'required|string',
            'cultural_group_id' => 'nullable|exists:cultural_groups,id',
        ]);

        $teaching->update($validated);

        return to_route('dashboard.teachings.index')->with('success', 'Teaching updated.');
    }

    public function destroy(Teaching $teaching): RedirectResponse
    {
        $teaching->delete();

        return to_route('dashboard.teachings.index')->with('success', 'Teaching deleted.');
    }
}
