<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Models\MonsterStaggerLimit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MonsterStaggerLimit
 */
class MonsterStaggerLimitResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'region' => $this->region,
            'value' => $this->value,
            'value_cut' => $this->value_cut,
            'extract_color' => $this->extract_color,
        ];
    }
}
