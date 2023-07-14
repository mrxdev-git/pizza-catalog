<?php

namespace App\Http\Controllers;

use App\Models\Pizza;
use Illuminate\Http\Request;

class PizzaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pizza = Pizza::all();
        return response()->json($pizza);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string'
        ]);

        $pizza = Pizza::create($validatedData);
        return response()->json($pizza, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pizza $pizza)
    {
        $pizza->name = $request->input('name');
        $pizza->save();

        $pizza->ingredients()->sync($request->input('ingredients'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pizza $pizza)
    {
        $pizza->delete();
        return response()->noContent();
    }
}
