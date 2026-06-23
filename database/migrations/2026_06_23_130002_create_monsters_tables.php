<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monsters', function (Blueprint $table): void {
            $table->id();
            $table->string('class')->index();
            $table->string('name');
            $table->string('name_de');
            $table->string('name_fr');
            $table->string('name_es');
            $table->string('name_it');
            $table->string('name_jp');
            $table->string('signature_move');
            $table->string('trait');
            $table->string('icon_name')->nullable();
            $table->string('sort_name')->default('')->index();
        });

        Schema::create('monster_ailments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('monster_id')->index();
            $table->string('ailment');
        });

        Schema::create('monster_damage', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('monster_id')->index();
            $table->string('body_part');
            $table->integer('cut')->nullable();
            $table->integer('impact')->nullable();
            $table->integer('shot')->nullable();
            $table->integer('fire')->nullable();
            $table->integer('water')->nullable();
            $table->integer('ice')->nullable();
            $table->integer('thunder')->nullable();
            $table->integer('dragon')->nullable();
            $table->integer('ko')->nullable();
        });

        Schema::create('monster_weaknesses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('monster_id')->index();
            $table->string('state');
            $table->integer('fire');
            $table->integer('water');
            $table->integer('thunder');
            $table->integer('ice');
            $table->integer('dragon');
            $table->integer('poison');
            $table->integer('paralysis');
            $table->integer('sleep');
            $table->integer('pitfall_trap');
            $table->integer('shock_trap');
            $table->integer('flash_bomb');
            $table->integer('sonic_bomb');
            $table->integer('dung_bomb');
            $table->integer('meat');
        });

        Schema::create('monster_habitats', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('monster_id')->index();
            $table->foreignId('location_id')->index();
            $table->integer('start_area')->nullable();
            $table->string('move_area')->nullable();
            $table->integer('rest_area')->nullable();
        });

        Schema::create('monster_statuses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('monster_id')->index();
            $table->string('status');
            $table->integer('initial')->nullable();
            $table->integer('increase')->nullable();
            $table->integer('max')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('damage')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monster_statuses');
        Schema::dropIfExists('monster_habitats');
        Schema::dropIfExists('monster_weaknesses');
        Schema::dropIfExists('monster_damage');
        Schema::dropIfExists('monster_ailments');
        Schema::dropIfExists('monsters');
    }
};
