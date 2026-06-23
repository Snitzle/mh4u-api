<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Wyporium item-for-item trades, unlocked by a quest.
        Schema::create('wyporium', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('item_in_id')->index();
            $table->foreignId('item_out_id')->index();
            $table->foreignId('unlock_quest_id')->nullable()->index();
        });

        // Hunting Horn note combinations and the songs they play.
        Schema::create('horn_melodies', function (Blueprint $table): void {
            $table->id();
            $table->string('notes')->index();
            $table->string('song');
            $table->string('effect1');
            $table->string('effect2');
            $table->string('duration');
            $table->string('extension');
        });

        Schema::create('felyne_skills', function (Blueprint $table): void {
            $table->id();
            $table->string('skill_name');
            $table->text('description');
        });

        // Felyne kitchen: two ingredients cook into a dish granting felyne skills.
        Schema::create('food_combos', function (Blueprint $table): void {
            $table->id();
            $table->string('ingredient1');
            $table->string('ingredient2');
            $table->string('cooked');
            $table->string('bonus');
            $table->foreignId('skill1_id')->nullable()->index();
            $table->foreignId('skill2_id')->nullable()->index();
            $table->foreignId('skill3_id')->nullable()->index();
        });

        Schema::create('ingredients', function (Blueprint $table): void {
            $table->id();
            $table->string('ingredient');
            $table->string('name');
            $table->integer('level')->nullable();
            $table->foreignId('quest_id')->nullable()->index();
        });

        // Veggie Elder garden trades.
        Schema::create('veggie_elder', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('location_id')->index();
            $table->foreignId('offer_item_id')->index();
            $table->foreignId('receive_item_id')->index();
            $table->integer('quantity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('veggie_elder');
        Schema::dropIfExists('ingredients');
        Schema::dropIfExists('food_combos');
        Schema::dropIfExists('felyne_skills');
        Schema::dropIfExists('horn_melodies');
        Schema::dropIfExists('wyporium');
    }
};
