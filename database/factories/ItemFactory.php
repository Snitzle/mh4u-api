<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

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
            'type' => fake()->randomElement(['Ore', 'Bone', 'Consumable', 'Flesh']),
            'sub_type' => '',
            'rarity' => fake()->numberBetween(1, 10),
            'carry_capacity' => fake()->numberBetween(1, 99),
            'buy' => fake()->numberBetween(0, 5000),
            'sell' => fake()->numberBetween(0, 3000),
            'description' => fake()->sentence(),
            'icon_name' => ucfirst(fake()->word()).'.png',
            'armor_dupe_name_fix' => '',
        ];
    }
}
