<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'base_price'];

    // Un produit a plusieurs variations
    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }
}
