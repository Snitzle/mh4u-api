<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the "universal gap" fields sourced from Kiranico that the original
 * import did not carry: HP + rank multipliers, crown-size thresholds, enraged
 * modifiers, limping/capture thresholds, ecology text, plus per-monster
 * stagger (part-break) limits and raw trap durations.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monsters', function (Blueprint $table): void {
            // HP: base value and the Low/High/G rank multipliers.
            $table->integer('base_hp')->nullable();
            $table->decimal('hp_mult_low', 4, 2)->nullable();
            $table->decimal('hp_mult_high', 4, 2)->nullable();
            $table->decimal('hp_mult_g', 4, 2)->nullable();

            // Crown size thresholds (raw in-game size measure).
            $table->decimal('crown_mini', 7, 1)->nullable();
            $table->decimal('crown_large', 7, 1)->nullable();
            $table->decimal('crown_king', 7, 1)->nullable();
            $table->string('size_class')->nullable();

            // Enraged-state modifiers.
            $table->integer('rage_duration')->nullable();
            $table->decimal('rage_mod_attack', 4, 2)->nullable();
            $table->decimal('rage_mod_defense', 4, 2)->nullable();
            $table->decimal('rage_mod_speed', 4, 2)->nullable();

            // Limping (near-capture) and capture HP thresholds, as a percent of max HP.
            $table->integer('limp_low')->nullable();
            $table->integer('limp_high')->nullable();
            $table->integer('limp_high_apex')->nullable();
            $table->integer('limp_g')->nullable();
            $table->integer('limp_g_apex')->nullable();
            $table->integer('cap_low')->nullable();
            $table->integer('cap_high')->nullable();
            $table->integer('cap_high_apex')->nullable();
            $table->integer('cap_g')->nullable();
            $table->integer('cap_g_apex')->nullable();

            // Ecology / bestiary description.
            $table->text('ecology')->nullable();
        });

        Schema::create('monster_stagger_limits', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('monster_id')->index();
            $table->string('region');
            $table->integer('value')->nullable();
            $table->integer('value_cut')->nullable();
            $table->string('extract_color')->nullable();
            $table->integer('sort_order')->default(0);
        });

        Schema::create('monster_trap_effects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('monster_id')->index();
            $table->string('trap');
            $table->integer('normal')->nullable();
            $table->integer('enraged')->nullable();
            $table->integer('fatigued')->nullable();
            $table->integer('sort_order')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monster_trap_effects');
        Schema::dropIfExists('monster_stagger_limits');

        Schema::table('monsters', function (Blueprint $table): void {
            $table->dropColumn([
                'base_hp', 'hp_mult_low', 'hp_mult_high', 'hp_mult_g',
                'crown_mini', 'crown_large', 'crown_king', 'size_class',
                'rage_duration', 'rage_mod_attack', 'rage_mod_defense', 'rage_mod_speed',
                'limp_low', 'limp_high', 'limp_high_apex', 'limp_g', 'limp_g_apex',
                'cap_low', 'cap_high', 'cap_high_apex', 'cap_g', 'cap_g_apex',
                'ecology',
            ]);
        });
    }
};
