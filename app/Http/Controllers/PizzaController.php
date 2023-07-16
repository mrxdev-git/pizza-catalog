<?php

namespace App\Http\Controllers;

use App\Models\Pizza;
use Illuminate\Http\Request;
use App\Models\Ingredient;

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
        $request->validate([
            'name' => 'required',
            'ingredients' => 'required|array',
            'ingredients.*' => 'exists:ingredients,id',
        ]);

        $pizza = Pizza::create([
            'name' => $request->input('name'),
            'price' => 0
        ]);

        $this->processPizza($request, $pizza);

        return response()->json($pizza, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'ingredients' => 'required|array',
            'ingredients.*' => 'exists:ingredients,id',
        ]);

        $pizza = Pizza::findOrFail($id);

        $this->processPizza($request, $pizza);

        return response()->json($pizza, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pizza $pizza)
    {
        $pizza->delete();
        return response()->noContent();
    }

    protected function processPizza(Request $request, Pizza $pizza)
    {
        $pizza->name = $request->input('name');
        $pizza->price = 0;

        $ingredients = $request->input('ingredients');
        $order = 1;

        $pizza->ingredients()->detach();

        foreach ($ingredients as $ingredientId) {
            $ingredient = Ingredient::find($ingredientId);

            if ($ingredient) {
                $pizza->ingredients()->attach($ingredient, ['order' => $order]);
                $pizza->price += $ingredient->price;
                $order++;
            }
        }

        $pizza->price += ($pizza->price * 0.5);

        $pizza->save();
    }
}
