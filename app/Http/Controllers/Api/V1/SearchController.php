<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\SearchRequest;
use App\Models\Item;
use App\Models\Location;
use App\Models\Monster;
use App\Models\Quest;
use App\Models\SkillTree;
use App\Support\IconUrl;
use App\Support\Translator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

/**
 * Universal full-text search across monsters, equipment, items, quests,
 * locations and skill trees. Results are grouped by entity type, each hit
 * carrying enough to render and link to a detail page.
 */
class SearchController extends ApiController
{
    public function __invoke(SearchRequest $request): JsonResponse
    {
        $query = (string) $request->validated('q');
        $limit = (int) ($request->validated('limit') ?? 10);

        $requested = array_filter(explode(',', (string) $request->validated('types', '')));
        $wantsAll = $requested === [];
        $enabled = fn (string $group): bool => $wantsAll || in_array($group, $requested, true);

        /** @var array<string, list<array<string, mixed>>> $groups */
        $groups = [];

        if ($enabled('monsters')) {
            foreach (Monster::search($query)->take($limit)->get() as $monster) {
                $groups['monsters'][] = $this->hit('monster', $monster->id, $monster, IconUrl::monster($monster->icon_name), 'api.v1.monsters.show');
            }
        }

        // Items cover plain items plus the weapon/armor/decoration entities.
        if ($enabled('items') || $enabled('weapons') || $enabled('armor') || $enabled('decorations')) {
            $items = Item::search($query)->take($limit)->get()->load(['weapon', 'armor']);

            foreach ($items as $item) {
                [$group, $type, $route] = $this->classifyItem($item->type);

                if (! $enabled($group)) {
                    continue;
                }

                $icon = match ($type) {
                    'weapon' => IconUrl::weapon($item->weapon?->icon_name),
                    'armor' => IconUrl::armor($item->armor?->icon_name),
                    default => IconUrl::item($item->icon_name),
                };

                $groups[$group][] = $this->hit($type, $item->id, $item, $icon, $route);
            }
        }

        if ($enabled('quests')) {
            foreach (Quest::search($query)->take($limit)->get() as $quest) {
                $groups['quests'][] = $this->hit('quest', $quest->id, $quest, null, 'api.v1.quests.show');
            }
        }

        if ($enabled('locations')) {
            foreach (Location::search($query)->take($limit)->get() as $location) {
                $groups['locations'][] = $this->hit('location', $location->id, $location, IconUrl::location($location->map), 'api.v1.locations.show');
            }
        }

        if ($enabled('skill_trees')) {
            foreach (SkillTree::search($query)->take($limit)->get() as $skillTree) {
                $groups['skill_trees'][] = $this->hit('skill_tree', $skillTree->id, $skillTree, null, 'api.v1.skill-trees.show');
            }
        }

        return response()->json([
            'data' => $groups,
            'meta' => ['query' => $query],
        ]);
    }

    /**
     * @return array{string, string, string} [group, type, route name]
     */
    private function classifyItem(string $type): array
    {
        return match ($type) {
            'Weapon' => ['weapons', 'weapon', 'api.v1.weapons.show'],
            'Armor' => ['armor', 'armor', 'api.v1.armor.show'],
            'Decoration' => ['decorations', 'decoration', 'api.v1.decorations.show'],
            default => ['items', 'item', 'api.v1.items.show'],
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function hit(string $type, int $id, Model $model, ?string $iconUrl, string $route): array
    {
        return [
            'type' => $type,
            'id' => $id,
            'name' => Translator::resolve($model, 'name'),
            'icon_url' => $iconUrl,
            'url' => route($route, $id),
        ];
    }
}
