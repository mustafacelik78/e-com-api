<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tmp_sale_price = $this->faker->randomFloat(0, 20, 5000);
        $remainder = $tmp_sale_price % 10;
        if ($remainder == 0){
            $sale_price_new = $tmp_sale_price - 0.01;
        } else {
            $sale_price_new = $tmp_sale_price - $remainder + 9.99;
        }
        return [
            'name' => $this->faker->word() . ' ' . $this->faker->word() . ' ' . $this->faker->word() . ' ' . $this->faker->word(),
            'category' => $this->faker->numberBetween(1,10),
            'price' => $sale_price_new,
            'stock' => $this->faker->numberBetween(0,150),
        ];
    }
}
