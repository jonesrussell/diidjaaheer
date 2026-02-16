<?php

use App\Models\Event;
use App\Models\Group;
use App\Models\Teaching;

test('database seeder creates events, groups, and teachings', function () {
    $this->seed();

    expect(Event::count())->toBeGreaterThanOrEqual(6);
    expect(Group::count())->toBeGreaterThanOrEqual(4);
    expect(Teaching::count())->toBeGreaterThanOrEqual(6);
});
