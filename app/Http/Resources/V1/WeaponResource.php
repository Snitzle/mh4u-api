<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Http\Resources\Concerns\ResolvesTranslations;
use App\Models\Weapon;
use App\Support\IconUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Weapon
 */
class WeaponResource extends JsonResource
{
    use ResolvesTranslations;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $item = $this->item;
        $isRanged = $this->wtype->isRanged();
        $isBowgun = $this->wtype->isBowgun();

        return [
            'id' => $this->id,
            'name' => $item !== null ? $this->translate($item, 'name') : null,
            'wtype' => $this->wtype,
            'rarity' => $item?->rarity,
            'icon_url' => IconUrl::weapon($this->icon_name),
            'tree_depth' => $this->tree_depth,
            'final' => $this->final,
            'creation_cost' => $this->creation_cost,
            'upgrade_cost' => $this->upgrade_cost,
            'attack' => $this->attack,
            'max_attack' => $this->max_attack,
            'affinity' => $this->affinity,
            'defense' => $this->defense,
            'num_slots' => $this->num_slots,
            'element' => $this->when($this->element !== null, fn (): array => [
                'type' => $this->element,
                'attack' => $this->element_attack,
            ]),
            'element_2' => $this->when($this->element_2 !== null, fn (): array => [
                'type' => $this->element_2,
                'attack' => $this->element_2_attack,
            ]),
            'awaken' => $this->when($this->awaken !== null, fn (): array => [
                'type' => $this->awaken,
                'attack' => $this->awaken_attack,
            ]),

            // Melee-only attributes.
            'sharpness' => $this->when(! $isRanged, $this->sharpness),
            'shelling_type' => $this->when($this->wtype->value === 'Gunlance', $this->shelling_type),
            'phial' => $this->when(in_array($this->wtype->value, ['Switch Axe', 'Charge Blade'], true), $this->phial),
            'horn_notes' => $this->when($this->wtype->isHuntingHorn(), $this->horn_notes),

            // Ranged-only attributes.
            'charges' => $this->when($this->wtype->value === 'Bow', $this->charges),
            'coatings' => $this->when($this->wtype->value === 'Bow', $this->coatings),
            'ammo' => $this->when($isBowgun, $this->ammo),
            'special_ammo' => $this->when($isBowgun, $this->special_ammo),
            'rapid_fire' => $this->when($isBowgun, $this->rapid_fire),
            'recoil' => $this->when($isBowgun, $this->recoil),
            'reload_speed' => $this->when($isBowgun, $this->reload_speed),
            'deviation' => $this->when($isBowgun, $this->deviation),

            // Relations.
            'parent' => $this->whenLoaded('parent', fn () => WeaponSummaryResource::make($this->parent)),
            'children' => WeaponSummaryResource::collection($this->whenLoaded('children')),
            'melodies' => HornMelodyResource::collection($this->whenLoaded('hornMelodies')),
            'components' => ComponentResource::collection($this->whenLoaded('componentsRequired')),
        ];
    }
}
