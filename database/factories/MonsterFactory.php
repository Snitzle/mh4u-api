<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Monster;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Monster>
 */
class MonsterFactory extends Factory
{
    protected $model = Monster::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'class' => fake()->randomElement(['Flying Wyvern', 'Brute Wyvern', 'Fanged Beast', 'Leviathan']),
            'name' => $name,
            'name_de' => "{$name} DE",
            'name_fr' => "{$name} FR",
            'name_es' => "{$name} ES",
            'name_it' => "{$name} IT",
            'name_jp' => "{$name} JP",
            'signature_move' => fake()->words(2, true),
            'trait' => fake()->word(),
            'icon_name' => 'MH4U-'.str_replace(' ', '_', ucwords((string) $name)).'_Icon.png',
            'sort_name' => $name,
        ];
    }
}
