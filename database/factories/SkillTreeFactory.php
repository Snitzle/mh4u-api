<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SkillTree;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SkillTree>
 */
class SkillTreeFactory extends Factory
{
    protected $model = SkillTree::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'name_de' => "{$name} DE",
            'name_fr' => "{$name} FR",
            'name_es' => "{$name} ES",
            'name_it' => "{$name} IT",
            'name_jp' => "{$name} JP",
        ];
    }
}
