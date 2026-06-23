<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Models\MonsterWeakness;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MonsterWeakness
 */
class MonsterWeaknessResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'state' => $this->state,
            'elements' => [
                'fire' => $this->fire,
                'water' => $this->water,
                'thunder' => $this->thunder,
                'ice' => $this->ice,
                'dragon' => $this->dragon,
            ],
            'ailments' => [
                'poison' => $this->poison,
                'paralysis' => $this->paralysis,
                'sleep' => $this->sleep,
            ],
            'traps' => [
                'pitfall_trap' => $this->pitfall_trap,
                'shock_trap' => $this->shock_trap,
                'flash_bomb' => $this->flash_bomb,
                'sonic_bomb' => $this->sonic_bomb,
                'dung_bomb' => $this->dung_bomb,
                'meat' => $this->meat,
            ],
        ];
    }
}
