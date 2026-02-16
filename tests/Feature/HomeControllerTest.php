<?php

use JonesRussell\NorthCloud\Models\Article;
use JonesRussell\NorthCloud\Models\NewsSource;

test('homepage returns latest published articles', function () {
    $source = NewsSource::create([
        'name' => 'Test Source',
        'slug' => 'test-source',
        'url' => 'https://example.com',
    ]);

    Article::create([
        'title' => 'Published Article',
        'slug' => 'published-article',
        'url' => 'https://example.com/1',
        'external_id' => 'ext-1',
        'news_source_id' => $source->id,
        'status' => 'published',
        'published_at' => now()->subHour(),
    ]);

    Article::create([
        'title' => 'Draft Article',
        'slug' => 'draft-article',
        'url' => 'https://example.com/2',
        'external_id' => 'ext-2',
        'news_source_id' => $source->id,
        'status' => 'draft',
        'published_at' => null,
    ]);

    $response = $this->get('/');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Home/Index')
        ->has('latestNews', 1)
        ->where('latestNews.0.title', 'Published Article')
    );
});

test('homepage returns empty arrays when no content exists', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Home/Index')
        ->has('latestNews', 0)
        ->has('powwowEvents', 0)
        ->has('groups', 0)
    );
});
