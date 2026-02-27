<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CulturalGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CulturalGroupController extends Controller
{
    public function index(Request $request): Response
    {
        $culturalGroups = CulturalGroup::query()
            ->with('parent')
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->depth_type, fn ($q) => $q->where('depth_type', $request->depth_type))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('dashboard/cultural-groups/Index', [
            'culturalGroups' => $culturalGroups,
            'filters' => $request->only(['search', 'depth_type']),
            'stats' => [
                'total' => CulturalGroup::count(),
                'root' => CulturalGroup::where('depth_type', 'root')->count(),
                'family' => CulturalGroup::where('depth_type', 'family')->count(),
                'group' => CulturalGroup::where('depth_type', 'group')->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('dashboard/cultural-groups/Create', [
            'parentOptions' => CulturalGroup::select('id', 'name', 'depth_type')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:cultural_groups',
            'depth_type' => 'required|in:root,family,group,sub_group,community,clan',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:cultural_groups,id',
            'sort_order' => 'nullable|integer',
        ]);

        CulturalGroup::create($validated);

        return to_route('dashboard.cultural-groups.index')->with('success', 'Cultural group created.');
    }

    public function edit(CulturalGroup $culturalGroup): Response
    {
        return Inertia::render('dashboard/cultural-groups/Edit', [
            'culturalGroup' => $culturalGroup->load('parent'),
            'parentOptions' => CulturalGroup::where('id', '!=', $culturalGroup->id)
                ->select('id', 'name', 'depth_type')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(Request $request, CulturalGroup $culturalGroup): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:cultural_groups,slug,'.$culturalGroup->id,
            'depth_type' => 'required|in:root,family,group,sub_group,community,clan',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:cultural_groups,id',
            'sort_order' => 'nullable|integer',
        ]);

        $culturalGroup->update($validated);

        return to_route('dashboard.cultural-groups.index')->with('success', 'Cultural group updated.');
    }

    public function destroy(CulturalGroup $culturalGroup): RedirectResponse
    {
        $culturalGroup->delete();

        return to_route('dashboard.cultural-groups.index')->with('success', 'Cultural group deleted.');
    }
}
