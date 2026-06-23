<?php

declare(strict_types=1);

use App\Models\Monster;
use App\Models\Quest;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('filters quests by star rank', function (): void {
    Quest::factory()->create(['stars' => 5]);
    Quest::factory()->create(['stars' => 2]);

    $this->getJson('/api/v1/quests?filter[stars]=5')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.stars', 5);
});

it('filters quests by monster', function (): void {
    $monster = Monster::factory()->create();
    $withMonster = Quest::factory()->create();
    $withMonster->monsters()->attach($monster->id);
    Quest::factory()->create(); // a quest without the monster

    $this->getJson("/api/v1/quests?filter[monster]={$monster->id}")
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $withMonster->id);
});
