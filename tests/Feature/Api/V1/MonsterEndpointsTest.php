<?php

declare(strict_types=1);

use App\Models\HuntingReward;
use App\Models\Item;
use App\Models\Monster;
use App\Models\MonsterWeakness;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists monsters with a lean, paginated, multilingual payload', function (): void {
    Monster::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/monsters');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [['id', 'name' => ['en', 'de', 'jp'], 'class', 'icon_url']],
            'meta' => ['total', 'per_page', 'current_page'],
        ])
        ->assertJsonPath('meta.total', 3);
});

it('flattens names to a single language with ?lang', function (): void {
    $monster = Monster::factory()->create();

    $this->getJson('/api/v1/monsters?lang=de')
        ->assertOk()
        ->assertJsonPath('data.0.name', $monster->name_de);
});

it('builds absolute icon URLs from the configured asset base', function (): void {
    $monster = Monster::factory()->create();

    $this->getJson("/api/v1/monsters/{$monster->id}")
        ->assertOk()
        ->assertJsonPath('data.icon_url', "http://localhost/assets/monsters/{$monster->icon_name}");
});

it('embeds weaknesses and rank-grouped hunting rewards on detail', function (): void {
    $monster = Monster::factory()->create();
    MonsterWeakness::factory()->create(['monster_id' => $monster->id]);
    HuntingReward::factory()->create([
        'monster_id' => $monster->id,
        'item_id' => Item::factory(),
        'rank' => 'LR',
    ]);

    $this->getJson("/api/v1/monsters/{$monster->id}")
        ->assertOk()
        ->assertJsonCount(1, 'data.weaknesses')
        ->assertJsonCount(1, 'data.hunting_rewards.LR')
        ->assertJsonPath('data.hunting_rewards.LR.0.rank', 'LR');
});

it('returns the standard error envelope for an unknown monster', function (): void {
    $this->getJson('/api/v1/monsters/999999')
        ->assertNotFound()
        ->assertExactJson([
            'error' => [
                'status' => 404,
                'code' => 'not_found',
                'message' => 'The requested resource was not found.',
            ],
        ]);
});
