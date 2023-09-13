<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\Umbrella;
use Illuminate\Http\Request;
use App\Models\BeachZone;

class OrdersController extends Controller
{
    public function index(Request $request)
    {

        if ($request->has('userId')) {
            $result = [];
            $orders = Order::with(['price', 'umbrella'])
                ->where('user_id', $request->input('userId'))
                ->get();
            foreach ($orders as $order) {
                $zone = $order->umbrella->beachzone; // Accedi alla relazione 'zone' definita nel modello Umbrella
                $result[] = [
                    'orders' => $order,
                    'zone' => $zone,
                ];
            }

            return response()->json(
                $result
            );
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
