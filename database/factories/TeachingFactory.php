<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Teaching;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Teaching> */
class TeachingFactory extends Factory
{
    protected $model = Teaching::class;

    public function definition(): array
    {
        $teachings = [
            'culture' => [
                'Seven Grandfather Teachings',
                'The Medicine Wheel',
                'Smudging Ceremony',
                'Clan System and Governance',
            ],
            'history' => [
                'The Three Fires Confederacy',
                'Treaty of Niagara 1764',
                'Migration from the Eastern Seaboard',
                'The Dish With One Spoon Wampum',
            ],
            'language' => [
                'Introduction to Anishinaabemowin',
                'Everyday Greetings and Phrases',
                'Numbers and Counting',
                'Seasons and Nature Words',
            ],
        ];

        $type = fake()->randomElement(array_keys($teachings));
        $title = fake()->randomElement($teachings[$type]);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 9999),
            'type' => $type,
            'content' => fake()->paragraphs(3, true),
        ];
    }
}
