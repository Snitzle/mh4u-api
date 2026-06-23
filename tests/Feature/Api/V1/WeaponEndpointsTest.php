<?php

declare(strict_types=1);

use App\Models\Item;
use App\Models\Weapon;
use App\Models\WeaponAmmo;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('omits bowgun-only fields for a melee weapon', function (): void {
    $weapon = Weapon::factory()->create(['wtype' => 'Great Sword']);

    $response = $this->getJson("/api/v1/weapons/{$weapon->id}");

    $response->assertOk()
        ->assertJsonPath('data.wtype', 'Great Sword')
        ->assertJsonMissingPath('data.ammo')
        ->assertJsonMissingPath('data.reload_speed');
});

it('exposes bowgun-only fields for a bowgun', function (): void {
    $weapon = Weapon::factory()->create(['wtype' => 'Light Bowgun', 'reload_speed' => 'Fast']);
    $ammoItem = Item::factory()->create(['type' => 'Ammo']);
    WeaponAmmo::create([
        'weapon_id' => $weapon->id,
        'item_id' => $ammoItem->id,
        'capacity' => 5,
        'capacity_special' => null,
    ]);

    $this->getJson("/api/v1/weapons/{$weapon->id}")
        ->assertOk()
        ->assertJsonPath('data.reload_speed', 'Fast')
        ->assertJsonPath('data.ammo.0.item', $ammoItem->name)
        ->assertJsonPath('data.ammo.0.capacity', 5);
});

it('filters weapons by type', function (): void {
    Weapon::factory()->create(['wtype' => 'Great Sword']);
    Weapon::factory()->create(['wtype' => 'Long Sword']);

    $this->getJson('/api/v1/weapons?filter[wtype]=Long Sword')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.wtype', 'Long Sword');
});

it('filters weapons by rarity (via the shared item)', function (): void {
    $rare = Item::factory()->create(['type' => 'Weapon', 'rarity' => 8]);
    Weapon::factory()->create(['id' => $rare->id]);
    $common = Item::factory()->create(['type' => 'Weapon', 'rarity' => 2]);
    Weapon::factory()->create(['id' => $common->id]);

    $this->getJson('/api/v1/weapons?filter[rarity]=8')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.rarity', 8);
});
