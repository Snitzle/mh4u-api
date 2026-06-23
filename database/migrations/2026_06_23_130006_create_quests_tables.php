<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quests', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('goal');
            $table->string('hub')->index();
            $table->string('type')->index();
            $table->unsignedInteger('stars')->index();
            $table->foreignId('location_id')->index();
            $table->unsignedInteger('time_limit');
            $table->unsignedInteger('fee');
            $table->unsignedInteger('reward');
            $table->unsignedInteger('hrp')->nullable();
            $table->text('sub_goal')->nullable();
            $table->unsignedInteger('sub_reward')->nullable();
            $table->unsignedInteger('sub_hrp')->nullable();
        });

        Schema::create('quest_rewards', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('quest_id')->index();
            $table->foreignId('item_id')->index();
            $table->string('reward_slot')->index();
            $table->integer('percentage');
            $table->integer('stack_size');
        });

        // Self-referential: a quest requires another quest to be completed first.
        Schema::create('quest_prereqs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('quest_id')->index();
            $table->foreignId('prereq_id')->index();
        });

        // Pivot: which monsters appear in a quest (`unstable` = randomly added).
        Schema::create('monster_quest', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('monster_id')->index();
            $table->foreignId('quest_id')->index();
            $table->string('unstable')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monster_quest');
        Schema::dropIfExists('quest_prereqs');
        Schema::dropIfExists('quest_rewards');
        Schema::dropIfExists('quests');
    }
};
