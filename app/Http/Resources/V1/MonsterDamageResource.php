<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Models\MonsterDamage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MonsterDamage
 */
class MonsterDamageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'body_part' => $this->body_part,
            'cut' => $this->cut,
            'impact' => $this->impact,
            'shot' => $this->shot,
            'fire' => $this->fire,
            'water' => $this->water,
            'ice' => $this->ice,
            'thunder' => $this->thunder,
            'dragon' => $this->dragon,
            'ko' => $this->ko,
        ];
    }
}
