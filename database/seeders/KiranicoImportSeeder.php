<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Comprehensive Kiranico merge: populates the net-new tables/columns the
 * original mh4u.db import did not carry (detailed weapon sharpness, bowgun ammo,
 * weapon/armor/monster asset refs, map sub-areas, quest supplies, quest columns,
 * armor set grouping) from the scraped JSON in database/source/kiranico/.
 *
 * The JSON is gitignored (never committed), so this seeder skips gracefully when
 * the data is absent (e.g. in CI). Idempotent: child tables are rebuilt.
 *
 * Id reconciliation: items/quests/maps share Kiranico's id space, so they key by
 * id; weapons/armor use a separate Kiranico id space, so they match by (unique)
 * name to our shared-PK id; monsters likewise match by name.
 */
class KiranicoImportSeeder extends Seeder
{
    private const array COLOURS = ['red', 'orange', 'yellow', 'green', 'blue', 'white', 'purple'];

    public function run(): void
    {
        // Decoding the larger scraped files (weapons ~11 MB) spikes memory.
        ini_set('memory_limit', '512M');

        $dir = (string) config('mh4u.kiranico_data');

        if (! is_dir($dir)) {
            $this->command->warn("Kiranico data dir not found ({$dir}); skipping comprehensive import.");

            return;
        }

        $this->importMonsters($dir);
        $this->importWeapons($dir);
        $this->importArmor($dir);
        $this->importMaps($dir);
        $this->importQuests($dir);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function load(string $dir, string $entity): array
    {
        $path = "{$dir}/{$entity}.json";

        if (! is_file($path)) {
            return [];
        }

        /** @var array{records?: array<int, array<string, mixed>>} $decoded */
        $decoded = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        return $decoded['records'] ?? [];
    }

    private function norm(string $name): string
    {
        // Keep '+': it is the discriminator between upgrade tiers (e.g. "Iron
        // Sword" vs "Iron Sword+"), which otherwise collide and mis-key rows.
        return preg_replace('/[^a-z0-9+]/', '', strtolower($name)) ?? '';
    }

    /**
     * Normalised name => our id, for an equipment table sharing items.id.
     *
     * @return array<string, int>
     */
    private function idByName(string $table): array
    {
        $map = [];

        /** @var array<string, int> $rows */
        $rows = DB::table($table)
            ->join('items', 'items.id', '=', "{$table}.id")
            ->pluck("{$table}.id", 'items.name')
            ->all();

        foreach ($rows as $name => $id) {
            $map[$this->norm((string) $name)] = (int) $id;
        }

        return $map;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function insertChunked(string $table, array $rows): void
    {
        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table($table)->insert($chunk);
        }
    }

    private function importMonsters(string $dir): void
    {
        $records = $this->load($dir, 'monsters');
        if ($records === []) {
            return;
        }

        $idByName = [];
        foreach (DB::table('monsters')->pluck('id', 'name')->all() as $name => $id) {
            $idByName[$this->norm((string) $name)] = (int) $id;
        }

        DB::table('monster_sounds')->delete();

        $sounds = [];
        foreach ($records as $monster) {
            $id = $idByName[$this->norm((string) $monster['local_name'])] ?? null;
            if ($id === null) {
                continue;
            }
            foreach ($monster['monstersounds'] ?? [] as $sound) {
                $sounds[] = ['monster_id' => $id, 'filename' => (string) $sound['filename']];
            }
        }

        $this->insertChunked('monster_sounds', $sounds);
        $this->command->info(sprintf('  monsters: %d sound refs', count($sounds)));
    }

    private function importWeapons(string $dir): void
    {
        $records = $this->load($dir, 'weapons');
        if ($records === []) {
            return;
        }

        $idByName = $this->idByName('weapons');

        foreach (['weapon_sharpness', 'weapon_ammo', 'weapon_models', 'weapon_sounds'] as $table) {
            DB::table($table)->delete();
        }

        $sharpness = [];
        $ammo = [];
        $models = [];
        $sounds = [];

        foreach ($records as $weapon) {
            $id = $idByName[$this->norm((string) $weapon['local_name'])] ?? null;
            if ($id === null) {
                continue;
            }

            if (! empty($weapon['weaponsharpness'])) {
                /** @var array<string, mixed> $s */
                $s = $weapon['weaponsharpness'];
                $row = ['weapon_id' => $id];
                foreach (self::COLOURS as $colour) {
                    $row[$colour] = $s[$colour] ?? null;
                    $row[$colour.'_plus'] = $s[$colour.'_plus'] ?? null;
                }
                $sharpness[] = $row;
            }

            foreach ($weapon['weapongunnerammos'] ?? [] as $a) {
                /** @var array<string, mixed> $pivot */
                $pivot = $a['pivot'] ?? [];
                $special = (int) ($pivot['capacity_special'] ?? -1);
                $ammo[] = [
                    'weapon_id' => $id,
                    'item_id' => (int) ($pivot['item_id'] ?? 0),
                    'capacity' => $pivot['capacity'] ?? null,
                    'capacity_special' => $special < 0 ? null : $special,
                ];
            }

            foreach ($weapon['weaponmodels'] ?? [] as $m) {
                $models[] = ['weapon_id' => $id, 'filename' => (string) $m['filename'], 'model_index' => $m['index'] ?? null];
            }

            foreach ($weapon['weaponsounds'] ?? [] as $s) {
                $sounds[] = ['weapon_id' => $id, 'filename' => (string) $s['filename']];
            }
        }

        $this->insertChunked('weapon_sharpness', $sharpness);
        $this->insertChunked('weapon_ammo', $ammo);
        $this->insertChunked('weapon_models', $models);
        $this->insertChunked('weapon_sounds', $sounds);
        $this->command->info(sprintf('  weapons: %d sharpness, %d ammo, %d models, %d sounds', count($sharpness), count($ammo), count($models), count($sounds)));
    }

    private function importArmor(string $dir): void
    {
        $records = $this->load($dir, 'armor');
        if ($records === []) {
            return;
        }

        $idByName = $this->idByName('armor');
        DB::table('armor_models')->delete();

        $models = [];
        foreach ($records as $armor) {
            $id = $idByName[$this->norm((string) $armor['local_name'])] ?? null;
            if ($id === null) {
                continue;
            }
            if (isset($armor['armorset_id'])) {
                DB::table('armor')->where('id', $id)->update(['armorset_id' => (int) $armor['armorset_id']]);
            }
            foreach ($armor['armormodels'] ?? [] as $m) {
                $models[] = ['armor_id' => $id, 'filename' => (string) $m['filename']];
            }
        }

        $this->insertChunked('armor_models', $models);
        $this->command->info(sprintf('  armor: %d model refs', count($models)));
    }

    private function importMaps(string $dir): void
    {
        $records = $this->load($dir, 'maps');
        if ($records === []) {
            return;
        }

        /** @var array<int, true> $locationIds */
        $locationIds = DB::table('locations')->pluck('id')->flip()->all();
        DB::table('map_areas')->delete();

        $areas = [];
        foreach ($records as $map) {
            $id = (int) $map['id'];
            if (! isset($locationIds[$id])) {
                continue;
            }
            $order = 0;
            foreach ($map['mapareas'] ?? [] as $area) {
                /** @var array<string, mixed> $pivot */
                $pivot = $area['pivot'] ?? [];
                $areas[] = [
                    'location_id' => $id,
                    'kiranico_area_id' => $pivot['maparea_id'] ?? $area['id'] ?? null,
                    'area_name' => (string) $area['local_name'],
                    'hot_drink' => $pivot['hot_drink'] ?? null,
                    'cool_drink' => $pivot['cool_drink'] ?? null,
                    'torch' => $pivot['torch'] ?? null,
                    'pitfall_trap' => $pivot['pitfall_trap'] ?? null,
                    'shock_trap' => $pivot['shock_trap'] ?? null,
                    'sort_order' => $order++,
                ];
            }
        }

        $this->insertChunked('map_areas', $areas);
        $this->command->info(sprintf('  maps: %d areas', count($areas)));
    }

    private function importQuests(string $dir): void
    {
        $records = $this->load($dir, 'quests');
        if ($records === []) {
            return;
        }

        /** @var array<int, true> $questIds */
        $questIds = DB::table('quests')->pluck('id')->flip()->all();
        DB::table('quest_supplies')->delete();

        $supplies = [];
        foreach ($records as $quest) {
            $id = (int) $quest['id'];
            if (! isset($questIds[$id])) {
                continue;
            }

            /** @var array{local_name?: string}|null $priority */
            $priority = $quest['questpriority'] ?? null;
            DB::table('quests')->where('id', $id)->update([
                'priority' => $priority['local_name'] ?? null,
                'hrp_required' => $quest['hrp_required'] ?? null,
                'map_time' => $quest['map_time'] ?? null,
            ]);

            $order = 0;
            foreach ($quest['supplies'] ?? [] as $supply) {
                /** @var array<string, mixed> $pivot */
                $pivot = $supply['pivot'] ?? [];
                $supplies[] = [
                    'quest_id' => $id,
                    'item_id' => (int) ($pivot['item_id'] ?? $supply['id'] ?? 0),
                    'quantity' => $pivot['quantity'] ?? null,
                    'sort_order' => $order++,
                ];
            }
        }

        $this->insertChunked('quest_supplies', $supplies);
        $this->command->info(sprintf('  quests: %d supply rows', count($supplies)));
    }
}
