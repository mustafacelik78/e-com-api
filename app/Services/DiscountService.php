<?php
namespace App\Services;

use App\Models\DiscountRule;
use App\Models\DiscountUse;
use App\Models\Order;
use App\Models\OrderDiscount;
use Illuminate\Support\Facades\DB;

class DiscountService
{
    public function applyDiscounts(Order $order)
    {
        $discounts = DiscountRule::all();
        $totalDiscount = 0;
        $appliedDiscounts = [];

        foreach ($discounts as $discount) {
            $conditions = $discount->conditions;
            $discountAmount = 0;

            switch ($discount->type) {
                case 'PERCENTAGE':
                    if ($order->total >= $conditions['min_total'] && $this->canApplyDiscount($discount, $order->customer_id, $order->id, $conditions)) {
                        $discountAmount = $order->total * ($discount->value / 100);
                    }
                    break;

                case 'BUY_X_GET_Y_FREE':
                    if ($this->canApplyDiscount($discount, $order->customer_id, $order->id, $conditions)) {
                        foreach ($order->orderProducts as $orderProduct) {
                            if ($orderProduct->product->category == $conditions['category_id']) {
                                $freeItems = intdiv($orderProduct->quantity, $conditions['x']) * $conditions['y'];
                                $discountAmount = $freeItems * $orderProduct->product->price;
                            }
                        }
                    }
                    break;

                case 'CATEGORY_PERCENTAGE':
                    if ($this->canApplyDiscount($discount, $order->customer_id, $order->id, $conditions)) {
                        $categoryProducts = $order->orderProducts->filter(fn($p) => $p->product->category == $conditions['category_id']);
                        if ($categoryProducts->count() >= $conditions['min_quantity']) {
                            if (isset($conditions['sort']) && $conditions['sort'] && $conditions['sort'] == 'desc')
                            {
                                $productPrice = $categoryProducts->sortByDesc('product.price')->first();
                            } else {
                                $productPrice = $categoryProducts->sortBy('product.price')->first();
                            }

                            $discountAmount = $productPrice->product->price * ($discount->value / 100);
                        }
                    }
                    break;

                case 'LIMITED_USE':
                    if ($this->canApplyDiscount($discount, $order->customer_id, $order->id, $conditions)) {
                        $discountAmount = $discount->value;
                    }
                    break;
            }

            if ($discountAmount > 0) {

                $existingDiscountUse = DiscountUse::where('discount_rule_id', $discount->id)
                    ->where('customer_id', $order->customer_id)
                    ->where('order_id', $order->id)
                    ->first();

                if (!$existingDiscountUse) {
                    DiscountUse::create([
                        'discount_rule_id' => $discount->id,
                        'customer_id' => $order->customer_id,
                        'order_id' => $order->id,
                        'usage_count' => 1,
                    ]);
                }

                $subtotal = $order->total - $totalDiscount - $discountAmount;

                OrderDiscount::create([
                    'order_id' => $order->id,
                    'discount_rule_id' => $discount->id,
                    'discount_reason' => $discount->type,
                    'discount_amount' => $discountAmount,
                    'subtotal' => $subtotal,
                ]);

                $appliedDiscounts[] = [
                    'discountReason' => $discount->name,
                    'discountAmount' => number_format($discountAmount, 2, '.', ''),
                    'subtotal' => number_format($subtotal, 2, '.', ''),
                ];

                $totalDiscount += $discountAmount;
            }
        }

        return [
            'orderId' => $order->id,
            'discounts' => $appliedDiscounts,
            'totalDiscount' => number_format($totalDiscount, 2, '.', ''),
            'discountedTotal' => number_format($order->total - $totalDiscount, 2, '.', ''),
        ];
    }

    private function canApplyDiscount($discount, $customerId, $orderId, $conditions)
    {
        if (!isset($conditions['customer_limit'])) {
            return true;
        }

        $usage = DiscountUse::where('discount_rule_id', $discount->id)
            ->where('customer_id', $customerId)
            ->where('order_id', '<>' , $orderId)
            ->first();

        return !$usage || $usage->usage_count < $conditions['customer_limit'];
    }


}
