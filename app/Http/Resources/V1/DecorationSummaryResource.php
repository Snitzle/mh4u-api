<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Http\Resources\Concerns\ResolvesTranslations;
use App\Models\Decoration;
use App\Support\IconUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Decoration
 */
class DecorationSummaryResource extends JsonResource
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
            'rarity' => $item?->rarity,
            'num_slots' => $this->num_slots,
            'icon_url' => $item !== null ? IconUrl::item($item->icon_name) : null,
        ];
    }
}
