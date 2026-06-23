<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Item;
use App\Models\Weapon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Weapon>
 */
class WeaponFactory extends Factory
{
    protected $model = Weapon::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Shares its primary key with a freshly created item.
            'id' => Item::factory()->state(['type' => 'Weapon']),
            'parent_id' => null,
            'wtype' => 'Great Sword',
            'attack' => fake()->numberBetween(100, 400),
            'affinity' => '0%',
            'num_slots' => 0,
            'tree_depth' => 0,
            'final' => true,
            'icon_name' => 'great_sword5.png',
        ];
    }
}
