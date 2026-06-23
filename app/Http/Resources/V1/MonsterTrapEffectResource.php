<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Models\MonsterTrapEffect;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MonsterTrapEffect
 */
class MonsterTrapEffectResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'trap' => $this->trap,
            'normal' => $this->normal,
            'enraged' => $this->enraged,
            'fatigued' => $this->fatigued,
        ];
    }
}
