<?php

namespace App\Http\Controllers;

use App\Models\Variant;
use Illuminate\Http\Request;

class variantController extends Controller
{
    public function index()
    {
        return Variant::with('product', 'color', 'size')->get();
    }

    public function show($id)
    {
        return Variant::with('product', 'color', 'size')->findOrFail($id);
    }
}
