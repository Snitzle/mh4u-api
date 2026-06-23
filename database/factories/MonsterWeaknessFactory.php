<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Monster;
use App\Models\MonsterWeakness;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MonsterWeakness>
 */
class MonsterWeaknessFactory extends Factory
{
    protected $model = MonsterWeakness::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'monster_id' => Monster::factory(),
            'state' => 'Normal',
            'fire' => fake()->numberBetween(0, 3),
            'water' => fake()->numberBetween(0, 3),
            'thunder' => fake()->numberBetween(0, 3),
            'ice' => fake()->numberBetween(0, 3),
            'dragon' => fake()->numberBetween(0, 3),
            'poison' => fake()->numberBetween(0, 3),
            'paralysis' => fake()->numberBetween(0, 3),
            'sleep' => fake()->numberBetween(0, 3),
            'pitfall_trap' => 1,
            'shock_trap' => 1,
            'flash_bomb' => 1,
            'sonic_bomb' => 0,
            'dung_bomb' => 1,
            'meat' => 0,
        ];
    }
}
