<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'product_code' => 'P'.fake()->unique()->numerify('#####'),
            'product_name' => ucfirst(fake()->words(2, true)).' '.fake()->randomElement([100, 150, 250, 500]).'ml',
            'stock' => fake()->numberBetween(50, 500),
            'price' => fake()->numberBetween(5, 150) * 1000,
            'low_stock_threshold' => 5,
        ];
    }
}
