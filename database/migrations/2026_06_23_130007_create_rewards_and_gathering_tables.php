<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Carve / capture / break / shiny rewards from hunting a monster.
        Schema::create('hunting_rewards', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('item_id')->index();
            $table->foreignId('monster_id')->index();
            $table->string('condition');
            $table->string('rank')->index();
            $table->integer('stack_size');
            $table->integer('percentage');
        });

        // Gatherable items at a location (by area/site and rank).
        Schema::create('gathering', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('item_id')->index();
            $table->foreignId('location_id')->index();
            $table->string('area');
            $table->string('site');
            $table->string('rank')->index();
            $table->integer('quantity')->nullable();
            $table->integer('percentage')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gathering');
        Schema::dropIfExists('hunting_rewards');
    }
};
