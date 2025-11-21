<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        $seed = $this->faker->unique()->numberBetween(1, 9999);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(100, 999),
            'description' => $this->faker->paragraphs(2, true),
            'price' => $this->faker->numberBetween(50000, 500000),
            'stock' => $this->faker->numberBetween(0, 50),
            'weight' => $this->faker->numberBetween(100, 2000),
            // menggunakan placeholder image dari picsum
            'image' => "https://picsum.photos/seed/product{$seed}/800/600",
            'is_active' => $this->faker->boolean(85),
        ];
    }
}
