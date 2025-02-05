<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\DiscountService;

class DiscountController extends Controller
{
    protected $discountService;

    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }

    public function applyDiscounts(Order $order)
    {
        $result = $this->discountService->applyDiscounts($order);

        return response()->json($result);
    }

    public function getAllOrdersWithDiscounts()
    {
        $orders = Order::with('orderProducts', 'orderDiscounts')->get();

        $response = $orders->map(function ($order) {
            $totalDiscount = $order->orderDiscounts->sum('discount_amount');
            return [
                'orderId' => $order->id,
                'total' => number_format($order->total, 2, '.', ''),
                'totalDiscount' => number_format($totalDiscount, 2, '.', ''),
                'discountedTotal' => number_format($order->total - $totalDiscount, 2, '.', ''),
                'discounts' => $order->orderDiscounts->map(function ($discount) {
                    return [
                        'discountReason' => $discount->discount_reason,
                        'discountAmount' => number_format($discount->discount_amount, 2, '.', ''),
                        'subtotal' => number_format($discount->subtotal, 2, '.', ''),
                    ];
                }),
            ];
        });

        return response()->json($response);
    }

}
