<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Group;
use Inertia\Inertia;
use Inertia\Response;
use JonesRussell\NorthCloud\Models\Article;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Home/Index', [
            'latestNews' => Article::published()
                ->with(['newsSource', 'tags'])
                ->latest('published_at')
                ->take(6)
                ->get()
                ->map(fn (Article $article) => [
                    'id' => $article->id,
                    'title' => $article->title,
                    'category' => $article->tags->first()?->name ?? 'News',
                    'source' => $article->newsSource?->name,
                    'time' => $article->published_at?->diffForHumans(),
                    'url' => $article->url,
                    'image_url' => $article->image_url,
                    'excerpt' => $article->excerpt,
                ]),
            'powwowEvents' => Event::where('starts_at', '>=', now())
                ->orderBy('starts_at')
                ->take(6)
                ->get()
                ->map(fn (Event $event) => [
                    'id' => $event->id,
                    'day' => $event->starts_at->format('d'),
                    'month' => $event->starts_at->format('M'),
                    'name' => $event->title,
                    'location' => $event->location,
                    'type' => $event->type,
                ]),
            'groups' => Group::take(4)
                ->get()
                ->map(fn (Group $group) => [
                    'id' => $group->id,
                    'name' => $group->name,
                    'type' => $group->type,
                    'region' => $group->region,
                    'url' => $group->url,
                ]),
        ]);
    }
}
