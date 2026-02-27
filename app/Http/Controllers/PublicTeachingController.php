<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Teaching;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicTeachingController extends Controller
{
    public function index(Request $request): Response
    {
        $teachings = Teaching::query()
            ->with('culturalGroup:id,name,slug')
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->when($request->search, fn ($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('teachings/Index', [
            'teachings' => $teachings,
            'filters' => $request->only(['type', 'search']),
        ]);
    }

    public function show(string $slug): Response
    {
        $teaching = Teaching::where('slug', $slug)
            ->with('culturalGroup:id,name,slug')
            ->firstOrFail();

        return Inertia::render('teachings/Show', [
            'teaching' => $teaching,
            'culturalGroup' => $teaching->culturalGroup,
        ]);
    }
}
