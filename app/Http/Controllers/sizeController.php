<?php

namespace App\Http\Controllers;
use App\Models\Size;
use Illuminate\Http\Request;

class sizeController extends Controller
{
    public function index()
    {
        return Size::all();
    }

    public function show($id)
    {
        return Size::findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        return Size::create($validated);
    }

}
