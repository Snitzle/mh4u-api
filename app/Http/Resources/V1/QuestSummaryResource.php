<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Models\Quest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Quest
 */
class QuestSummaryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'hub' => $this->hub,
            'type' => $this->type,
            'stars' => $this->stars,
        ];
    }
}
