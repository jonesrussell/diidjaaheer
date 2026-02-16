<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Group> */
class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition(): array
    {
        $names = [
            'Anishinaabe Cultural Network',
            'Ojibwe Language Society',
            'Great Lakes Indigenous Youth Council',
            'Turtle Island Elders Circle',
            'Anishinaabemowin Revitalization Project',
            'First Nations Women\'s Association',
            'Indigenous Artists Collective',
            'Woodland Drum Circle',
        ];

        $name = fake()->unique()->randomElement($names);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'type' => fake()->randomElement(['online', 'offline']),
            'url' => fake()->optional(0.6)->url(),
            'description' => fake()->optional()->sentence(),
            'region' => fake()->randomElement([
                'Great Lakes',
                'Ontario',
                'Manitoba',
                'Ontario / Manitoba',
                'Minnesota / Wisconsin',
                'Northern Ontario',
            ]),
        ];
    }
}
