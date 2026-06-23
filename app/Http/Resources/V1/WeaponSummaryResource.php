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
class WeaponSummaryResource extends JsonResource
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
            'wtype' => $this->wtype,
            'rarity' => $item?->rarity,
            'attack' => $this->attack,
            'num_slots' => $this->num_slots,
            'final' => $this->final,
            'icon_url' => IconUrl::weapon($this->icon_name),
        ];
    }
}
