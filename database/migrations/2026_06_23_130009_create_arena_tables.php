<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arena_quests', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('goal');
            $table->foreignId('location_id')->index();
            $table->unsignedInteger('reward');
            $table->integer('num_participants');
            $table->string('time_s');
            $table->string('time_a');
            $table->string('time_b');
        });

        Schema::create('arena_rewards', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('arena_id')->index();
            $table->foreignId('item_id')->index();
            $table->integer('percentage');
            $table->integer('stack_size');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arena_rewards');
        Schema::dropIfExists('arena_quests');
    }
};
