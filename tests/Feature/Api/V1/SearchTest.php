<?php

declare(strict_types=1);

use App\Models\Item;
use App\Models\Monster;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('groups search hits by entity type', function (): void {
    Monster::factory()->create(['name' => 'Tigrex', 'sort_name' => 'Tigrex']);
    Item::factory()->create(['name' => 'Tigrex Scale', 'type' => 'Bone']);

    $response = $this->getJson('/api/v1/search?q=Tigrex&lang=en');

    $response->assertOk()
        ->assertJsonPath('meta.query', 'Tigrex')
        ->assertJsonPath('data.monsters.0.type', 'monster')
        ->assertJsonPath('data.monsters.0.name', 'Tigrex')
        ->assertJsonPath('data.items.0.type', 'item');
});

it('limits search to requested types', function (): void {
    Monster::factory()->create(['name' => 'Rathalos']);
    Item::factory()->create(['name' => 'Rathalos Scale']);

    $response = $this->getJson('/api/v1/search?q=Rathalos&types=monsters&lang=en');

    $response->assertOk()
        ->assertJsonPath('data.monsters.0.name', 'Rathalos')
        ->assertJsonMissingPath('data.items');
});

it('requires a query term', function (): void {
    $this->getJson('/api/v1/search')
        ->assertStatus(422)
        ->assertJsonPath('error.code', 'validation_failed')
        ->assertJsonPath('error.fields.q.0', 'The q field is required.');
});
