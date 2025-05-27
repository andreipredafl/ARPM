<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CartItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /*
    
        I did not tested the code, I tried to fix it on the fly
    
        1. There were in initial code a lot of problems regarding n+1 queries -> I eager loaded relations for avoiding this.
        2. There was Orders::all and after was maniputated and was problem also for performance
        3. Inefficient logic, sorting, manipuation -> I clened up the code to make it more readable
        4. I used collections to be better
    */
    public function index()
    {
        // Get orderss with eager loaded relations
        $orders = Order::with(['customer', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get all order IDs
        $orderIds = $orders->pluck('id');
        $cartItems = CartItem::whereIn('order_id', $orderIds)
            ->select('order_id'. 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('order_id')
            ->map->first();
            
        $orderData = [];

        foreach ($orders as $order) {
            // Calculate total for each
            $totalAmount = $order->items->sum(function($item) {
                return $item->price * $item->quantity;
            });
            
            $itemsCount = $order->items->count();

            // Get cart item from loaded data
            $lastCartItem = $cartItems->get($order->id);

            $orderData[] = [
                'order_id' => $order->id,
                'customer_name' => $order->customer->name,
                'total_amount' => $totalAmount,
                'items_count' => $itemsCount,
                'last_added_to_cart' => $lastCartItm ? $lastCartItm->created_at : null,
                'completed_order_exists' => $order->status === 'completed',
                'created_at' => $order->created_at,
            ];
        }

        return view('orders.index', [
            'orders' => $orderData
        ]);
    }
}