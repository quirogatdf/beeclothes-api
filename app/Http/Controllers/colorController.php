<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;

class colorController extends Controller
{
    public function index()
    {
        return Color::all();
    }

    public function show($id)
    {
       return Color::findOrFail($id);
    }
}
