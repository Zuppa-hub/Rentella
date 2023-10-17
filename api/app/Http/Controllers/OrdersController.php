<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Requests\OrdersFilterRequest;
use App\Models\Order;

class OrdersController extends Controller
{
    public function index(OrdersFilterRequest $request)
    {
        if ($request->has('userId')) {
            $result = [];
            $now = now();
            $active = boolval($request->input('active'));
            $orders = Order::with(['price', 'umbrella'])
                ->where('user_id', $request->input('userId'));

            if ($active === true) {
                // active orders
                $orders->where('end_date', '>', $now);
            } elseif ($active === false) {
                // non active orders
                $orders->where('end_date', '<=', $now);
            }
            $orders = $orders->get();
            foreach ($orders as $order) {
                $zone = $order->umbrella->beachzone;
                $result[] = [
                    'orders' => $order,
                    'name' => $zone->name
                ];
            }
            return response()->json($result);
        }
        $orders = Order::all();
        return response()->json($orders);
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
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
