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
            $active = $request->input('active');

            $orders = Order::with(['price', 'umbrella'])
                ->where('user_id', $request->input('userId'));

            if ($active === 'true') {
                // active orders
                $orders->where('end_date', '>', $now);
            } elseif ($active === 'false') {
                // non active orders
                $orders->where('end_date', '<=', $now);
            }

            $orders = $orders->get();

            foreach ($orders as $order) {
                $zone = $order->umbrella->beachzone; // Accedi alla relazione 'beachzone' definita nel modello Umbrella
                $result[] = [
                    'orders' => $order,
                    'zone' => $zone,
                ];
            }

            return response()->json($result);
        }

        $orders = Order::all();
        return response()->json($orders);
    }


    public function show($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($order);
    }

    public function store(OrderRequest $request)
    {
        $order = Order::create($request->all());
        return response()->json(['message' => 'Order data saved successfully', 'data' => $order], 201);
    }

    public function update(OrderRequest $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->update($request->all());
        return response()->json(['message' => 'Order data updated successfully', 'data' => $order], 200);
    }

    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->delete();
        return response()->json(['message' => 'Order data removed successfully', 'id' => $id]);
    }
}
