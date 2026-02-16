<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Group;
use App\Models\Teaching;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Event::factory(6)->create();
        Group::factory(4)->create();
        Teaching::factory(6)->create();
    }
}
