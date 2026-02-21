<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index()
    {
        return Sale::with('variant', 'orderDetail')->get();
    }

    public function show($id)
    {
        return Sale::with('variant', 'orderDetail')->findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'variant_id' => 'required|exists:variants,id',
            'order_detail_id' => 'nullable|exists:order_details,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'sale_date' => 'required|date',
            'status' => 'sometimes|in:completed,cancelled,refunded',
            'customer_name' => 'nullable|string|max:255',
        ]);

        $validated['profit'] = ($validated['unit_price'] - $validated['unit_cost']) * $validated['quantity'];
        $validated['status'] = $validated['status'] ?? 'completed';

        return Sale::create($validated);
    }

    public function update(Request $request, $id)
    {
        $sale = Sale::findOrFail($id);

        $validated = $request->validate([
            'variant_id' => 'sometimes|required|exists:variants,id',
            'order_detail_id' => 'nullable|exists:order_details,id',
            'quantity' => 'sometimes|required|integer|min:1',
            'unit_price' => 'sometimes|required|numeric|min:0',
            'unit_cost' => 'sometimes|required|numeric|min:0',
            'sale_date' => 'sometimes|required|date',
            'status' => 'sometimes|in:completed,cancelled,refunded',
            'customer_name' => 'nullable|string|max:255',
        ]);

        if (isset($validated['unit_price']) || isset($validated['unit_cost']) || isset($validated['quantity'])) {
            $unitPrice = $validated['unit_price'] ?? $sale->unit_price;
            $unitCost = $validated['unit_cost'] ?? $sale->unit_cost;
            $quantity = $validated['quantity'] ?? $sale->quantity;
            $validated['profit'] = ($unitPrice - $unitCost) * $quantity;
        }

        $sale->update($validated);
        return $sale;
    }

    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);
        $sale->delete();
        return response()->json(null, 204);
    }
}
