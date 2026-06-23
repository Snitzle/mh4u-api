<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_trees', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('name_de');
            $table->string('name_fr');
            $table->string('name_es');
            $table->string('name_it');
            $table->string('name_jp');
        });

        Schema::create('skills', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('skill_tree_id')->index();
            $table->integer('required_skill_tree_points');
            $table->string('name');
            $table->string('name_de');
            $table->string('name_fr');
            $table->string('name_es');
            $table->string('name_it');
            $table->string('name_jp');
            $table->text('description');
            $table->text('description_de');
            $table->text('description_fr');
            $table->text('description_es');
            $table->text('description_it');
            $table->text('description_jp');
        });

        // Pivot: which items (armor/charms/decorations) grant points toward a
        // skill tree. `point_value` may be negative.
        Schema::create('item_skill_tree', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('item_id')->index();
            $table->foreignId('skill_tree_id')->index();
            $table->integer('point_value');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_skill_tree');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('skill_trees');
    }
};
