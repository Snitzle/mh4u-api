<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the net-new data scraped from Kiranico that the original mh4u.db import
 * did not carry: detailed weapon sharpness, bowgun ammo capacities, weapon/armor/
 * monster asset filename refs, map sub-areas (with tool/drink availability),
 * quest supply items, plus a few quest columns and armor set grouping.
 *
 * All keyed by our existing ids (weapons/armor reconciled by name during import,
 * since Kiranico uses a separate id space for those).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weapon_sharpness', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('weapon_id')->index();
            // Hits per colour at base sharpness, and at Sharpness+1.
            foreach (['red', 'orange', 'yellow', 'green', 'blue', 'white', 'purple'] as $colour) {
                $table->integer($colour)->nullable();
                $table->integer($colour.'_plus')->nullable();
            }
        });

        Schema::create('weapon_ammo', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('weapon_id')->index();
            $table->foreignId('item_id')->index();
            $table->integer('capacity')->nullable();
            $table->integer('capacity_special')->nullable();
        });

        Schema::create('weapon_models', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('weapon_id')->index();
            $table->string('filename');
            $table->integer('model_index')->nullable();
        });

        Schema::create('weapon_sounds', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('weapon_id')->index();
            $table->string('filename');
        });

        Schema::create('armor_models', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('armor_id')->index();
            $table->string('filename');
        });

        Schema::create('monster_sounds', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('monster_id')->index();
            $table->string('filename');
        });

        Schema::create('map_areas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('location_id')->index();
            $table->integer('kiranico_area_id')->nullable();
            $table->string('area_name');
            // Tool / drink usefulness in the area (Kiranico ratings; null = n/a).
            $table->integer('hot_drink')->nullable();
            $table->integer('cool_drink')->nullable();
            $table->integer('torch')->nullable();
            $table->integer('pitfall_trap')->nullable();
            $table->integer('shock_trap')->nullable();
            $table->integer('sort_order')->default(0);
        });

        Schema::create('quest_supplies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('quest_id')->index();
            $table->foreignId('item_id')->index();
            $table->integer('quantity')->nullable();
            $table->integer('sort_order')->default(0);
        });

        Schema::table('quests', function (Blueprint $table): void {
            $table->string('priority')->nullable();
            $table->integer('hrp_required')->nullable();
            $table->integer('map_time')->nullable();
        });

        Schema::table('armor', function (Blueprint $table): void {
            $table->integer('armorset_id')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('armor', fn (Blueprint $table) => $table->dropColumn('armorset_id'));
        Schema::table('quests', fn (Blueprint $table) => $table->dropColumn(['priority', 'hrp_required', 'map_time']));
        Schema::dropIfExists('quest_supplies');
        Schema::dropIfExists('map_areas');
        Schema::dropIfExists('monster_sounds');
        Schema::dropIfExists('armor_models');
        Schema::dropIfExists('weapon_sounds');
        Schema::dropIfExists('weapon_models');
        Schema::dropIfExists('weapon_ammo');
        Schema::dropIfExists('weapon_sharpness');
    }
};
