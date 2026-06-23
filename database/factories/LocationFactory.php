<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    protected $model = Location::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->city();

        return [
            'name' => $name,
            'name_de' => "{$name} DE",
            'name_fr' => "{$name} FR",
            'name_es' => "{$name} ES",
            'name_it' => "{$name} IT",
            'name_jp' => "{$name} JP",
            'map' => 'maps_'.str_replace(' ', '_', strtolower($name)).'.png',
        ];
    }
}
