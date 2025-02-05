<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DiscountRule;

class DiscountRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DiscountRule::insert([
            [
                'type' => 'PERCENTAGE',
                'name' => '10_PERCENT_OVER_1000',
                'conditions' => json_encode(['min_total' => 1000, 'customer_limit' => 10]),
                'value' => 10.00,
            ],
            [
                'type' => 'BUY_X_GET_Y_FREE',
                'name' => 'BUY_5_GET_1_FREE',
                'conditions' => json_encode(['category_id' => 2, 'x' => 6, 'y' => 1, 'customer_limit' => 10]),
                'value' => null,
            ],
            [
                'type' => 'CATEGORY_PERCENTAGE',
                'name' => 'CATEGORY_1_PERCENTAGE_20',
                'conditions' => json_encode(['category_id' => 1, 'min_quantity' => 2, 'customer_limit' => 10, 'sort' => 'asc']),
                'value' => 20.00,
            ],
            [
                'type' => 'LIMITED_USE',
                'name' => 'LIMITED_USE',
                'conditions' => json_encode(['customer_limit' => 1]),
                'value' => 50.00,
            ]
        ]);
    }
}
