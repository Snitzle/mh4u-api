<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Armor;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Armor>
 */
class ArmorFactory extends Factory
{
    protected $model = Armor::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Item::factory()->state(['type' => 'Armor']),
            'slot' => fake()->randomElement(['Head', 'Body', 'Arms', 'Waist', 'Legs']),
            'defense' => fake()->numberBetween(1, 100),
            'max_defense' => fake()->numberBetween(50, 200),
            'fire_res' => 0,
            'thunder_res' => 0,
            'dragon_res' => 0,
            'water_res' => 0,
            'ice_res' => 0,
            'gender' => 'Both',
            'hunter_type' => 'Both',
            'num_slots' => 0,
            'icon_name' => 'head1.png',
        ];
    }
}
