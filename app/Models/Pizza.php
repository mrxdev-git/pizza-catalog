<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pizza extends Model
{
    use HasFactory;

    protected $table = 'pizzas';
    protected $fillable = ['name', 'price'];

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class)
            ->withPivot('order')
            ->withTimestamps();
    }

    public function calculatePrice()
    {
        $total = $this->ingredients->sum(function ($ingredient) {
            return $ingredient->cost_price;
        });

        return $total + ($total * 0.5);
    }

}
