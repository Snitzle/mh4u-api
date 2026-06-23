<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->index();
            $table->string('name_de');
            $table->string('name_fr');
            $table->string('name_es');
            $table->string('name_it');
            $table->string('name_jp')->nullable();
            $table->string('type')->index();
            $table->string('sub_type');
            $table->unsignedInteger('rarity')->default(0)->index();
            $table->unsignedInteger('carry_capacity')->default(0);
            $table->unsignedInteger('buy')->nullable();
            $table->unsignedInteger('sell')->nullable();
            $table->text('description')->nullable();
            $table->string('icon_name')->nullable();
            // Disambiguates armor pieces that share an item name (from source data).
            $table->string('armor_dupe_name_fix')->default('');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
