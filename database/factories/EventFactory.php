<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Event> */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $names = [
            'Spring Traditional Powwow',
            'Gathering of Nations',
            'Anishinaabe Cultural Festival',
            'Ojibwe Language Immersion Camp',
            'Elder Teachings Circle',
            'Youth Drumming Workshop',
            'Treaty Day Celebration',
            'Midewiwin Ceremony',
            'Wild Rice Harvest Gathering',
            'Winter Storytelling Night',
        ];

        $locations = [
            'Sault Ste. Marie, ON',
            'Thunder Bay, ON',
            'Sudbury, ON',
            'Winnipeg, MB',
            'Duluth, MN',
            'Saginaw, MI',
            'Garden River First Nation, ON',
            'Batchewana First Nation, ON',
            'Red Lake, ON',
            'Kenora, ON',
        ];

        $title = fake()->randomElement($names);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 9999),
            'starts_at' => fake()->dateTimeBetween('+1 week', '+6 months'),
            'ends_at' => fn (array $attrs) => fake()->optional(0.7)->dateTimeBetween($attrs['starts_at'], Carbon::parse($attrs['starts_at'])->addDays(3)),
            'location' => fake()->randomElement($locations),
            'type' => fake()->randomElement(['powwow', 'gathering', 'ceremony']),
            'description' => fake()->optional()->paragraph(),
        ];
    }
}
