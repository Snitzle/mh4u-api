<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Http\Resources\Concerns\ResolvesTranslations;
use App\Models\Armor;
use App\Support\IconUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Armor
 */
class ArmorSummaryResource extends JsonResource
{
    use ResolvesTranslations;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $item = $this->item;

        return [
            'id' => $this->id,
            'name' => $item !== null ? $this->translate($item, 'name') : null,
            'slot' => $this->slot,
            'rarity' => $item?->rarity,
            'defense' => $this->defense,
            'max_defense' => $this->max_defense,
            'num_slots' => $this->num_slots,
            'icon_url' => IconUrl::armor($this->icon_name),
        ];
    }
}
