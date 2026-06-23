<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Models\HornMelody;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin HornMelody
 */
class HornMelodyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'notes' => $this->notes,
            'song' => $this->song,
            'effect1' => $this->effect1,
            'effect2' => $this->effect2,
            'duration' => $this->duration,
            'extension' => $this->extension,
        ];
    }
}
