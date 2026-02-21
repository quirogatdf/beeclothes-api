<?php

namespace App\Http\Controllers;

use App\Models\OrderDetail;
use Illuminate\Http\Request;

class OrderDetailController extends Controller
{
    public function index()
    {
        return OrderDetail::with('variant', 'order')->get();
    }

    public function show($id)
    {
        return OrderDetail::with('variant', 'order')->findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'variant_id' => 'required|exists:variants,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'observations' => 'nullable|string',
        ]);

        return OrderDetail::create($validated);
    }

    public function update(Request $request, $id)
    {
        $detail = OrderDetail::findOrFail($id);

        $validated = $request->validate([
            'order_id' => 'sometimes|required|exists:orders,id',
            'variant_id' => 'sometimes|required|exists:variants,id',
            'quantity' => 'sometimes|required|integer|min:1',
            'unit_price' => 'sometimes|required|numeric|min:0',
            'observations' => 'nullable|string',
        ]);

        $detail->update($validated);
        return $detail;
    }

    public function destroy($id)
    {
        $detail = OrderDetail::findOrFail($id);
        $detail->delete();
        return response()->json(null, 204);
    }
}
