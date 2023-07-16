<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ingredients = Ingredient::all();
        return response()->json($ingredients);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        $ingredient = Ingredient::create($validatedData);

        return response()->json($ingredient, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required',
            'price' => 'required|numeric|min:0',
        ]);

        $ingredient = Ingredient::findOrFail($id);
        $ingredient->name = $request->input('name');
        $ingredient->price = $request->input('price');
        $ingredient->save();

        if ($ingredient->isDirty('price')) {
            $pizzas = $ingredient->pizzas;

            if ($pizzas) {
                foreach($pizzas as $pizza) {
                    $ingredients_price = $pizza->ingredients()->sum('price');
                    $sellingPrice      = $ingredients_price + ($ingredients_price * 0.5);
                    $pizza->price      = $sellingPrice;
                    $pizza->save();
                }
            }
        }

        return response()->json($ingredient);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();

        return response()->noContent();
    }
}
