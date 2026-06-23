<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\ArmorController;
use App\Http\Controllers\Api\V1\DecorationController;
use App\Http\Controllers\Api\V1\HornMelodyController;
use App\Http\Controllers\Api\V1\ItemController;
use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Controllers\Api\V1\MonsterController;
use App\Http\Controllers\Api\V1\QuestController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\SkillTreeController;
use App\Http\Controllers\Api\V1\WeaponController;
use App\Http\Controllers\Api\V1\WyporiumController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->middleware([
        'throttle:api',
        // Reference data is static; allow CDN/browser caching with revalidation.
        'cache.headers:public;max_age='.config('mh4u.cache_ttl', 86400).';etag',
    ])
    ->group(function (): void {
        // Universal search
        Route::get('search', SearchController::class)->name('search');

        // Monsters
        Route::get('monsters', [MonsterController::class, 'index'])->name('monsters.index');
        Route::get('monsters/{monster}', [MonsterController::class, 'show'])->name('monsters.show');
        Route::get('monsters/{monster}/hunting-rewards', [MonsterController::class, 'huntingRewards'])->name('monsters.hunting-rewards');

        // Weapons
        Route::get('weapons', [WeaponController::class, 'index'])->name('weapons.index');
        Route::get('weapons/{weapon}', [WeaponController::class, 'show'])->name('weapons.show');
        Route::get('weapons/{weapon}/tree', [WeaponController::class, 'tree'])->name('weapons.tree');

        // Armor
        Route::get('armor', [ArmorController::class, 'index'])->name('armor.index');
        Route::get('armor/{armor}', [ArmorController::class, 'show'])->name('armor.show');

        // Decorations
        Route::get('decorations', [DecorationController::class, 'index'])->name('decorations.index');
        Route::get('decorations/{decoration}', [DecorationController::class, 'show'])->name('decorations.show');

        // Items
        Route::get('items', [ItemController::class, 'index'])->name('items.index');
        Route::get('items/{item}', [ItemController::class, 'show'])->name('items.show');
        Route::get('items/{item}/components', [ItemController::class, 'components'])->name('items.components');

        // Quests
        Route::get('quests', [QuestController::class, 'index'])->name('quests.index');
        Route::get('quests/{quest}', [QuestController::class, 'show'])->name('quests.show');
        Route::get('quests/{quest}/rewards', [QuestController::class, 'rewards'])->name('quests.rewards');

        // Locations
        Route::get('locations', [LocationController::class, 'index'])->name('locations.index');
        Route::get('locations/{location}', [LocationController::class, 'show'])->name('locations.show');
        Route::get('locations/{location}/gathering', [LocationController::class, 'gathering'])->name('locations.gathering');

        // Skill trees
        Route::get('skill-trees', [SkillTreeController::class, 'index'])->name('skill-trees.index');
        Route::get('skill-trees/{skillTree}', [SkillTreeController::class, 'show'])->name('skill-trees.show');

        // Lookups
        Route::get('wyporium', [WyporiumController::class, 'index'])->name('wyporium.index');
        Route::get('horn-melodies', [HornMelodyController::class, 'index'])->name('horn-melodies.index');
    });
