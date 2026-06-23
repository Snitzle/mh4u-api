<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Http\Resources\Concerns\ResolvesTranslations;
use App\Models\Location;
use App\Support\IconUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Location
 */
class LocationSummaryResource extends JsonResource
{
    use ResolvesTranslations;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->translate($this->resource, 'name'),
            'map_url' => IconUrl::location($this->map),
        ];
    }
}
