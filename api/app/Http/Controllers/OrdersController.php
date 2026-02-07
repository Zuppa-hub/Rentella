<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Requests\OrdersFilterRequest;
use App\Models\Order;

class OrdersController extends Controller
{
    public function index(OrdersFilterRequest $request)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $result = [];
        $now = now();
        $active = boolval($request->input('active'));
        
        // Always filter by authenticated user's ID
        $orders = Order::with(['price', 'umbrella', 'umbrella.beachzone.beach', 'umbrella.beachzone.beach.location'])
            ->where('user_id', $authUser->id);

        if ($request->has('active')) {
            if ($active === true) {
                // active orders
                $orders->where('end_date', '>', $now);
            } elseif ($active === false) {
                // non active orders
                $orders->where('end_date', '<=', $now);
            }
        }
        $orders = $orders->get();
        foreach ($orders as $order) {
            $zone = $order->umbrella->beachzone;
            $result[] = [
                'orders' => $order,
                'name' => $zone->beach->name,
            ];
        }
        return response()->json($result);
    }

    public function show($id)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $order = Order::findOrFail($id);
        
        // Users can only view their own orders
        if ($authUser->id !== $order->user_id) {
            return response()->json(['error' => 'Forbidden: You can only view your own orders'], 403);
        }
        
        return response()->json($order);
    }

    public function store(OrderRequest $request)
    {
        return response()->json(Order::create($request->all()), 201);
    }

    public function update(OrderRequest $request, $id)
    {
        return response()->json(Order::findOrFail($id)->update($request->all()), 200);
    }

    public function destroy($id)
    {
        return response()->json(Order::findOrFail($id)->delete());
    }
}
