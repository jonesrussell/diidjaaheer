<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CulturalGroup;
use Inertia\Inertia;
use Inertia\Response;

class CultureController extends Controller
{
    public function index(): Response
    {
        $groups = CulturalGroup::whereNull('parent_id')
            ->withCount('children')
            ->with('children:id,parent_id,name,slug,depth_type')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('culture/Index', [
            'groups' => $groups,
        ]);
    }

    public function show(string $slug): Response
    {
        $group = CulturalGroup::where('slug', $slug)->firstOrFail();

        $children = $group->children()
            ->withCount('children', 'teachings')
            ->get();

        $teachings = $group->teachings()
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        $breadcrumb = $group->ancestors()->push($group)->map(fn ($g) => [
            'name' => $g->name,
            'slug' => $g->slug,
        ])->values()->all();

        return Inertia::render('culture/Show', [
            'group' => $group,
            'children' => $children,
            'teachings' => $teachings,
            'breadcrumb' => $breadcrumb,
        ]);
    }
}
