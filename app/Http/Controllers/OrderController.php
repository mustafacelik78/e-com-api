<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $orders = Order::with(['customer', 'orderProducts.product'])->get();
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return DB::transaction(function () use ($request) {
            $total = 0;
            $order = Order::create([
                'customer_id' => $request->customer_id,
                'total' => 0,
            ]);

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    return response()->json([
                        'error' => "Stok yetersiz: {$product->name} (Mevcut: {$product->stock})"
                    ], Response::HTTP_BAD_REQUEST);
                }

                $product->decrement('stock', $item['quantity']);
                $total += $product->price * $item['quantity'];

                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'total' => $product->price * $item['quantity'],
                ]);
            }

            $order->update(['total' => $total]);
            return response()->json($order, Response::HTTP_CREATED);
        });
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(['message' => 'Sipariş silindi.'], Response::HTTP_OK);
    }
}
