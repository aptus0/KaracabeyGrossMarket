<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => 1,
            'name' => $name = $this->faker->words(3, true),
            'slug' => \Illuminate\Support\Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 100000),
            'description' => $this->faker->paragraph,
            'brand' => $this->faker->company,
            'barcode' => $this->faker->ean13,
            'price_cents' => $this->faker->numberBetween(1000, 50000),
            'stock_quantity' => $this->faker->numberBetween(0, 1000),
            'is_active' => true,
        ];
    }
}
