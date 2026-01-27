<?php

namespace App\Http\Controllers;

use App\Models\Variant;
use Illuminate\Http\Request;

class variantController extends Controller
{
    public function index()
    {
        return Variant::all();
    }

    public function show($id)
    {
        return Variant::findOrFail($id);
    }
}
