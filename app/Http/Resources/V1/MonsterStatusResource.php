<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Models\MonsterStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MonsterStatus
 */
class MonsterStatusResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'status' => $this->status,
            'initial' => $this->initial,
            'increase' => $this->increase,
            'max' => $this->max,
            'duration' => $this->duration,
            'damage' => $this->damage,
        ];
    }
}
