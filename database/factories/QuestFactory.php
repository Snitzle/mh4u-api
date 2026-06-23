<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Location;
use App\Models\Quest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quest>
 */
class QuestFactory extends Factory
{
    protected $model = Quest::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->sentence(3),
            'goal' => fake()->sentence(),
            'hub' => fake()->randomElement(['Caravan', 'Guild', 'Event']),
            'type' => fake()->randomElement(['Normal', 'Key', 'Urgent']),
            'stars' => fake()->numberBetween(1, 10),
            'location_id' => Location::factory(),
            'time_limit' => 50,
            'fee' => fake()->numberBetween(0, 2000),
            'reward' => fake()->numberBetween(0, 10000),
            'hrp' => fake()->numberBetween(0, 5000),
            'sub_goal' => null,
            'sub_reward' => null,
            'sub_hrp' => null,
        ];
    }
}
