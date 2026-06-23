<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Http\Resources\Concerns\ResolvesTranslations;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Skill
 */
class SkillResource extends JsonResource
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
            'required_skill_tree_points' => $this->required_skill_tree_points,
            'description' => $this->translate($this->resource, 'description'),
        ];
    }
}
