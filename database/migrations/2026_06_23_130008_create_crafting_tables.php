<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Crafting recipe lines: `created_item_id` is made from N of `component_item_id`.
        Schema::create('components', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('created_item_id')->index();
            $table->foreignId('component_item_id')->index();
            $table->integer('quantity');
            $table->string('type')->nullable();
        });

        // Combining two items into another (with a success percentage).
        Schema::create('combinations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('created_item_id')->index();
            $table->foreignId('item_1_id')->index();
            $table->foreignId('item_2_id')->index();
            $table->integer('amount_made_min');
            $table->integer('amount_made_max');
            $table->integer('percentage');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combinations');
        Schema::dropIfExists('components');
    }
};
