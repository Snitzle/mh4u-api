<?php

declare(strict_types=1);

use App\Models\Armor;
use App\Models\Item;
use App\Models\SkillTree;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('filters armor by slot', function (): void {
    Armor::factory()->create(['slot' => 'Head']);
    Armor::factory()->create(['slot' => 'Legs']);

    $this->getJson('/api/v1/armor?filter[slot]=Head')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.slot', 'Head');
});

it('filters armor by rarity (via the shared item)', function (): void {
    $rare = Item::factory()->create(['type' => 'Armor', 'rarity' => 9]);
    Armor::factory()->create(['id' => $rare->id]);
    $common = Item::factory()->create(['type' => 'Armor', 'rarity' => 1]);
    Armor::factory()->create(['id' => $common->id]);

    $this->getJson('/api/v1/armor?filter[rarity]=9')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.rarity', 9);
});

it('filters armor by granted skill tree', function (): void {
    $skill = SkillTree::factory()->create();
    $withSkill = Armor::factory()->create();
    $withSkill->skillTrees()->attach($skill->id, ['point_value' => 3]);
    Armor::factory()->create();

    $this->getJson("/api/v1/armor?filter[skill]={$skill->id}")
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $withSkill->id);
});
