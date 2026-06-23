<?php

declare(strict_types=1);

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('filters items by type', function (): void {
    Item::factory()->count(2)->create(['type' => 'Ore']);
    Item::factory()->count(3)->create(['type' => 'Bone']);

    $this->getJson('/api/v1/items?filter[type]=Ore')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.type', 'Ore');
});

it('clamps per_page to the maximum of 100', function (): void {
    Item::factory()->count(3)->create();

    $this->getJson('/api/v1/items?per_page=999')
        ->assertOk()
        ->assertJsonPath('meta.per_page', 100);
});

it('rejects an out-of-range per_page with a validation error', function (): void {
    $this->getJson('/api/v1/items?per_page=0')
        ->assertStatus(422)
        ->assertJsonPath('error.code', 'validation_failed');
});

it('rejects a disallowed filter', function (): void {
    Item::factory()->create();

    $this->getJson('/api/v1/items?filter[secret]=1')
        ->assertStatus(400);
});
