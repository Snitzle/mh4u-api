<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('name_de');
            $table->string('name_fr');
            $table->string('name_es');
            $table->string('name_it');
            $table->string('name_jp');
            $table->string('map');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
