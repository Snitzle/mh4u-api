<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Monster;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Tops up monsters with the "universal gap" fields sourced from Kiranico that
 * the original mh4u.db import did not carry — HP + rank multipliers, crown-size
 * thresholds, enraged modifiers, limping/capture thresholds and ecology text —
 * and rebuilds the monster_stagger_limits / monster_trap_effects tables.
 *
 * Reads the committed snapshot at database/source/kiranico.json (regenerate it
 * with `node database/source/kiranico-scrape.mjs`). Idempotent: scalar columns
 * are overwritten in place and the child tables are rebuilt from scratch.
 */
class KiranicoTopUpSeeder extends Seeder
{
    /** Monster columns copied verbatim from each snapshot entry. */
    private const MONSTER_COLUMNS = [
        'base_hp', 'hp_mult_low', 'hp_mult_high', 'hp_mult_g',
        'crown_mini', 'crown_large', 'crown_king', 'size_class',
        'rage_duration', 'rage_mod_attack', 'rage_mod_defense', 'rage_mod_speed',
        'limp_low', 'limp_high', 'limp_high_apex', 'limp_g', 'limp_g_apex',
        'cap_low', 'cap_high', 'cap_high_apex', 'cap_g', 'cap_g_apex',
        'ecology',
    ];

    public function run(): void
    {
        $path = database_path('source/kiranico.json');

        if (! is_file($path)) {
            $this->command->warn("Kiranico snapshot not found at {$path}; skipping universal-gap top-up.");

            return;
        }

        /** @var array<string, array<string, mixed>> $data */
        $data = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        // Rebuild the child tables from scratch so re-seeding stays idempotent.
        DB::table('monster_stagger_limits')->delete();
        DB::table('monster_trap_effects')->delete();

        $staggerRows = [];
        $trapRows = [];

        foreach ($data as $id => $monster) {
            $monsterId = (int) $id;

            $update = [];
            foreach (self::MONSTER_COLUMNS as $column) {
                $update[$column] = $monster[$column] ?? null;
            }
            Monster::query()->whereKey($monsterId)->update($update);

            /** @var array<int, array<string, mixed>> $stagger */
            $stagger = $monster['stagger'] ?? [];
            foreach ($stagger as $row) {
                $staggerRows[] = [
                    'monster_id' => $monsterId,
                    'region' => $row['region'],
                    'value' => $row['value'],
                    'value_cut' => $row['value_cut'],
                    'extract_color' => $row['extract_color'],
                    'sort_order' => $row['sort_order'],
                ];
            }

            /** @var array<int, array<string, mixed>> $traps */
            $traps = $monster['traps'] ?? [];
            foreach ($traps as $row) {
                $trapRows[] = [
                    'monster_id' => $monsterId,
                    'trap' => $row['trap'],
                    'normal' => $row['normal'],
                    'enraged' => $row['enraged'],
                    'fatigued' => $row['fatigued'],
                    'sort_order' => $row['sort_order'],
                ];
            }
        }

        foreach (array_chunk($staggerRows, 500) as $chunk) {
            DB::table('monster_stagger_limits')->insert($chunk);
        }

        foreach (array_chunk($trapRows, 500) as $chunk) {
            DB::table('monster_trap_effects')->insert($chunk);
        }

        $this->command->info(sprintf(
            'Kiranico top-up: %d monsters, %d stagger rows, %d trap rows.',
            count($data),
            count($staggerRows),
            count($trapRows),
        ));
    }
}
