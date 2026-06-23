<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Weapons, armor and decorations use table-per-type inheritance: each row's
 * primary key is the SAME value as its `items` row (no auto-increment here).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weapons', function (Blueprint $table): void {
            $table->unsignedBigInteger('id')->primary(); // == items.id
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->string('wtype')->index();
            $table->unsignedInteger('creation_cost')->nullable();
            $table->unsignedInteger('upgrade_cost')->nullable();
            $table->integer('attack');
            $table->integer('max_attack')->nullable();
            $table->string('element')->nullable();
            $table->integer('element_attack')->nullable();
            $table->string('element_2')->nullable();
            $table->integer('element_2_attack')->nullable();
            $table->string('awaken')->nullable();
            $table->integer('awaken_attack')->nullable();
            $table->integer('defense')->nullable();
            $table->text('sharpness')->nullable();
            $table->string('affinity');
            $table->string('horn_notes')->nullable();
            $table->string('shelling_type')->nullable();
            $table->string('phial')->nullable();
            $table->text('charges')->nullable();
            $table->text('coatings')->nullable();
            $table->string('recoil')->nullable();
            $table->string('reload_speed')->nullable();
            $table->text('rapid_fire')->nullable();
            $table->string('deviation')->nullable();
            $table->text('ammo')->nullable();
            $table->text('special_ammo')->nullable();
            $table->integer('num_slots');
            $table->integer('tree_depth');
            $table->boolean('final')->nullable();
            // Derived at import time (e.g. "great_sword5.png"); see Mh4uImportSeeder.
            $table->string('icon_name')->nullable();
        });

        Schema::create('armor', function (Blueprint $table): void {
            $table->unsignedBigInteger('id')->primary(); // == items.id
            $table->string('slot')->index();
            $table->integer('defense');
            $table->integer('max_defense')->nullable();
            $table->integer('fire_res');
            $table->integer('thunder_res');
            $table->integer('dragon_res');
            $table->integer('water_res');
            $table->integer('ice_res');
            $table->string('gender')->index();
            $table->string('hunter_type')->index();
            $table->integer('num_slots')->nullable();
            // Derived at import time (e.g. "head7.png"); see Mh4uImportSeeder.
            $table->string('icon_name')->nullable();
        });

        Schema::create('decorations', function (Blueprint $table): void {
            $table->unsignedBigInteger('id')->primary(); // == items.id
            $table->integer('num_slots');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decorations');
        Schema::dropIfExists('armor');
        Schema::dropIfExists('weapons');
    }
};
