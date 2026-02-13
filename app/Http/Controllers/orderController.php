<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class orderController extends Controller
{
    public function index()
    {
        return Order::with('supplier')->get();
    }

    public function show($id)
    {
        return Order::with('supplier')->findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'external_id' => 'required|string|max:255',
            'order_date' => 'nullable|date',
            'tracking_code' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'observations' => 'nullable|string',
        ]);

        return Order::create($validated);
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'supplier_id' => 'sometimes|required|exists:suppliers,id',
            'external_id' => 'sometimes|required|string|max:255',
            'order_date' => 'nullable|date',
            'tracking_code' => 'nullable|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'observations' => 'nullable|string',
        ]);

        $order->update($validated);
        return $order;
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(null, 204);
    }
}
