<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CulturalGroup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<CulturalGroup> */
class CulturalGroupFactory extends Factory
{
    protected $model = CulturalGroup::class;

    public function definition(): array
    {
        $name = ucwords(fake()->unique()->words(2, true));

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'depth_type' => fake()->randomElement(['root', 'family', 'group', 'sub_group', 'community', 'clan']),
            'description' => fake()->optional()->sentence(),
            'sort_order' => 0,
        ];
    }
}
