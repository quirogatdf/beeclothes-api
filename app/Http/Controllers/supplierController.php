<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class supplierController extends Controller
{
    public function index()
    {
        return Supplier::all();
    }

    public function show($id)
    {
        return Supplier::findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cuit' => 'required|string|unique:suppliers,cuit',
            'phone' => 'required|string|max:50',
            'mail' => 'required|email',
        ]);

        return Supplier::create($validated);
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'cuit' => 'sometimes|required|string|unique:suppliers,cuit,' . $id,
            'phone' => 'sometimes|required|string|max:50',
            'mail' => 'sometimes|required|email',
        ]);

        $supplier->update($validated);
        return $supplier;
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        return response()->json(null, 204);
    }
}
