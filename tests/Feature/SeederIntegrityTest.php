<?php

declare(strict_types=1);

use App\Models\Armor;
use App\Models\Decoration;
use App\Models\Monster;
use App\Models\Weapon;
use Database\Seeders\KiranicoTopUpSeeder;
use Database\Seeders\Mh4uImportSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

/**
 * Expected row counts captured from the authoritative source database. If a
 * future re-import drifts from these, this test fails loudly.
 *
 * @return array<string, int>
 */
function expectedRowCounts(): array
{
    return [
        'monsters' => 106,
        'monster_ailments' => 261,
        'monster_damage' => 986,
        'monster_weaknesses' => 112,
        'monster_habitats' => 147,
        'monster_statuses' => 784,
        'monster_stagger_limits' => 840,
        'monster_trap_effects' => 370,
        'items' => 7324,
        'weapons' => 2324,
        'armor' => 3087,
        'decorations' => 289,
        'skill_trees' => 144,
        'skills' => 277,
        'item_skill_tree' => 11947,
        'locations' => 20,
        'quests' => 569,
        'quest_rewards' => 10300,
        'quest_prereqs' => 319,
        'monster_quest' => 912,
        'hunting_rewards' => 4336,
        'gathering' => 7419,
        'components' => 23102,
        'combinations' => 168,
        'wyporium' => 128,
        'horn_melodies' => 143,
        'felyne_skills' => 67,
        'food_combos' => 84,
        'ingredients' => 24,
        'veggie_elder' => 126,
        'arena_quests' => 40,
        'arena_rewards' => 96,
    ];
}

beforeEach(function (): void {
    // The mh4u.db import + the monster universal-gap top-up. The heavy,
    // gitignored-data comprehensive import (KiranicoImportSeeder) is exercised
    // separately so this integrity suite stays fast and CI-portable.
    $this->seed(Mh4uImportSeeder::class);
    $this->seed(KiranicoTopUpSeeder::class);
});

test('every table imports the exact expected row count', function (): void {
    foreach (expectedRowCounts() as $table => $expected) {
        expect(DB::table($table)->count())->toBe($expected, "row count for {$table}");
    }
});

test('weapons, armor and decorations share a primary key with an item', function (): void {
    expect(Weapon::whereNotIn('id', DB::table('items')->pluck('id'))->count())->toBe(0)
        ->and(Armor::whereNotIn('id', DB::table('items')->pluck('id'))->count())->toBe(0)
        ->and(Decoration::whereNotIn('id', DB::table('items')->pluck('id'))->count())->toBe(0);
});

test('weapon and armor icon names are derived for every row', function (): void {
    expect(Weapon::whereNull('icon_name')->count())->toBe(0)
        ->and(Armor::whereNull('icon_name')->count())->toBe(0)
        ->and(Weapon::find(6001)->icon_name)->toBe('hunting_horn5.png');
});

test('foreign keys reference existing rows', function (): void {
    $orphanRewards = DB::table('hunting_rewards')
        ->whereNotIn('item_id', DB::table('items')->pluck('id'))
        ->orWhereNotIn('monster_id', DB::table('monsters')->pluck('id'))
        ->count();

    $orphanComponents = DB::table('components')
        ->whereNotIn('created_item_id', DB::table('items')->pluck('id'))
        ->orWhereNotIn('component_item_id', DB::table('items')->pluck('id'))
        ->count();

    expect($orphanRewards)->toBe(0)
        ->and($orphanComponents)->toBe(0);
});

test('the seeder is idempotent', function (): void {
    $this->seed(Mh4uImportSeeder::class);
    $this->seed(KiranicoTopUpSeeder::class);

    expect(DB::table('monsters')->count())->toBe(106)
        ->and(DB::table('items')->count())->toBe(7324)
        ->and(DB::table('monster_stagger_limits')->count())->toBe(840)
        ->and(DB::table('monster_trap_effects')->count())->toBe(370);
});

test('the Kiranico top-up populates the universal-gap fields', function (): void {
    $rathalos = Monster::findOrFail(72);

    expect($rathalos->base_hp)->toBe(4200)
        ->and($rathalos->rage_duration)->toBe(80)
        ->and($rathalos->cap_low)->toBe(23)
        ->and($rathalos->crown_king)->toEqual(2104.9)
        ->and($rathalos->ecology)->toContain('Kings of the Skies')
        ->and($rathalos->staggerLimits()->where('region', 'Head')->value('value'))->toEqual(230)
        ->and($rathalos->trapEffects()->where('trap', 'Pitfall Trap')->value('fatigued'))->toEqual(25);
});
