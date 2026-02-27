<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CulturalGroup;
use Illuminate\Database\Seeder;

class CulturalGroupSeeder extends Seeder
{
    public function run(): void
    {
        $root = CulturalGroup::create([
            'name' => 'Indigenous',
            'slug' => 'indigenous',
            'depth_type' => 'root',
            'description' => 'Indigenous peoples of Turtle Island and beyond.',
            'sort_order' => 0,
        ]);

        $anishinaabe = CulturalGroup::create([
            'name' => 'Anishinaabe',
            'slug' => 'anishinaabe',
            'depth_type' => 'family',
            'parent_id' => $root->id,
            'description' => 'The Anishinaabe are a group of culturally related Indigenous peoples in the Great Lakes region of Canada and the United States.',
            'sort_order' => 0,
        ]);

        $groups = [
            ['name' => 'Ojibwe', 'slug' => 'ojibwe', 'description' => 'Also known as Chippewa, one of the largest Indigenous groups in North America.', 'sort_order' => 0],
            ['name' => 'Odawa', 'slug' => 'odawa', 'description' => 'The Odawa (Ottawa) people, part of the Council of Three Fires.', 'sort_order' => 1],
            ['name' => 'Potawatomi', 'slug' => 'potawatomi', 'description' => 'The Potawatomi, Keepers of the Fire in the Council of Three Fires.', 'sort_order' => 2],
        ];

        foreach ($groups as $group) {
            CulturalGroup::create(array_merge($group, [
                'depth_type' => 'group',
                'parent_id' => $anishinaabe->id,
            ]));
        }
    }
}
