<?php

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
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'internal_code' => fake()->unique()->bothify('ITEM-####'),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'default_unit_price' => fake()->randomFloat(2, 1, 1000),
        ];
    }
}
