<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $fillable = ['product_id', 'price', 'stock'];

    // Une variation appartient à un produit
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Une variation de produit a plusieurs valeurs d'attributs (ex: "Taille: M", "Couleur: Rouge")
    public function attributes()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variation_attribute_value');
    }
}
