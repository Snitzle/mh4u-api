<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ArmorSlot;
use App\Enums\WeaponType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Imports the bundled MH4U SQLite database (database/source/mh4u.db, read via
 * the `sqlite_source` connection) into the application's relational schema.
 *
 * Normalizations applied during import:
 *  - `_id` => `id` on every row (source ids are preserved exactly).
 *  - Source table names are mapped to the idiomatic plural/pivot names.
 *  - Weapons/armor get a derived `icon_name` (e.g. "great_sword5.png").
 *  - Weapon `parent_id` of 0 (a tree root) becomes NULL.
 *
 * Empty source tables (charms, planting, trading, ...) and per-user state
 * (wishlist*, asb_sets) are intentionally not imported.
 */
class Mh4uImportSeeder extends Seeder
{
    private const int CHUNK = 1000;

    /**
     * Source table => destination table for straight 1:1 copies.
     */
    private const array TABLE_MAP = [
        'monsters' => 'monsters',
        'monster_ailment' => 'monster_ailments',
        'monster_damage' => 'monster_damage',
        'monster_weakness' => 'monster_weaknesses',
        'monster_habitat' => 'monster_habitats',
        'monster_status' => 'monster_statuses',
        'items' => 'items',
        'decorations' => 'decorations',
        'skill_trees' => 'skill_trees',
        'skills' => 'skills',
        'item_to_skill_tree' => 'item_skill_tree',
        'locations' => 'locations',
        'quests' => 'quests',
        'quest_rewards' => 'quest_rewards',
        'quest_prereqs' => 'quest_prereqs',
        'monster_to_quest' => 'monster_quest',
        'hunting_rewards' => 'hunting_rewards',
        'gathering' => 'gathering',
        'components' => 'components',
        'combining' => 'combinations',
        'wyporium' => 'wyporium',
        'horn_melodies' => 'horn_melodies',
        'felyne_skills' => 'felyne_skills',
        'food_combos' => 'food_combos',
        'ingredients' => 'ingredients',
        'veggie_elder' => 'veggie_elder',
        'arena_quests' => 'arena_quests',
        'arena_rewards' => 'arena_rewards',
    ];

    public function run(): void
    {
        foreach (self::TABLE_MAP as $source => $destination) {
            $this->copy($source, $destination);
        }

        $this->copyEquipment();
    }

    /**
     * Stream-copy a source table into its destination, mapping `_id` => `id`.
     *
     * @param  (callable(array<string, mixed>): array<string, mixed>)|null  $transform
     */
    private function copy(string $source, string $destination, ?callable $transform = null): void
    {
        DB::table($destination)->truncate();

        $integerColumns = $this->integerColumns($destination);
        $imported = 0;

        DB::connection('sqlite_source')
            ->table($source)
            ->orderBy('_id')
            ->chunk(self::CHUNK, function ($rows) use ($destination, $integerColumns, $transform, &$imported): void {
                $batch = [];

                foreach ($rows as $row) {
                    $data = (array) $row;
                    $data['id'] = $data['_id'];
                    unset($data['_id']);

                    // SQLite is loosely typed and stores non-numeric values in
                    // some integer cells (blanks, and text like "変動素材" =
                    // "variable" for a weapon's upgrade cost). Coerce anything
                    // non-numeric to null (or 0 where the column is NOT NULL)
                    // so MySQL's strict mode accepts the row.
                    foreach ($integerColumns as $column => $nullable) {
                        $value = $data[$column] ?? null;

                        if ($value !== null && ! is_numeric($value)) {
                            $data[$column] = $nullable ? null : 0;
                        }
                    }

                    if ($transform !== null) {
                        $data = $transform($data);
                    }

                    $batch[] = $data;
                }

                DB::table($destination)->insert($batch);
                $imported += count($batch);
            });

        $this->command->info(sprintf('  %-20s -> %-20s %6d rows', $source, $destination, $imported));
    }

    /**
     * Integer columns of a destination table, keyed by name with a nullable flag.
     *
     * @return array<string, bool>
     */
    private function integerColumns(string $table): array
    {
        $columns = [];

        foreach (Schema::getColumns($table) as $column) {
            if (str_contains($column['type_name'], 'int')) {
                $columns[$column['name']] = (bool) $column['nullable'];
            }
        }

        return $columns;
    }

    /**
     * Weapons and armor need a derived `icon_name` built from their rarity
     * (which lives on the shared `items` row) and their type/slot.
     */
    private function copyEquipment(): void
    {
        /** @var array<int, int> $rarities */
        $rarities = DB::connection('sqlite_source')
            ->table('items')
            ->pluck('rarity', '_id')
            ->all();

        $this->copy('weapons', 'weapons', function (array $data) use ($rarities): array {
            $data['parent_id'] = ($data['parent_id'] ?? 0) ?: null;
            $rarity = $rarities[$data['id']] ?? 0;
            $data['icon_name'] = WeaponType::from((string) $data['wtype'])->iconPrefix().$rarity.'.png';
            // Superseded by the structured weapon_sharpness / weapon_ammo tables.
            unset($data['sharpness'], $data['ammo']);

            return $data;
        });

        $this->copy('armor', 'armor', function (array $data) use ($rarities): array {
            $rarity = $rarities[$data['id']] ?? 0;
            $data['icon_name'] = ArmorSlot::from((string) $data['slot'])->iconPrefix().$rarity.'.png';

            return $data;
        });
    }
}
