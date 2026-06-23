<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\HuntingReward;
use App\Models\Item;
use App\Models\Monster;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HuntingReward>
 */
class HuntingRewardFactory extends Factory
{
    protected $model = HuntingReward::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'monster_id' => Monster::factory(),
            'condition' => fake()->randomElement(['Carve', 'Capture', 'Shiny Drop']),
            'rank' => fake()->randomElement(['LR', 'HR', 'G']),
            'stack_size' => 1,
            'percentage' => fake()->numberBetween(1, 100),
        ];
    }
}
