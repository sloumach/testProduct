<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    protected $fillable = ['attribute_id', 'value'];

    // Une valeur appartient à un attribut (ex: "S" appartient à "Taille")
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    // Une valeur d'attribut peut être utilisée dans plusieurs variations de produit
    public function productVariations()
    {
        return $this->belongsToMany(ProductVariation::class, 'product_variation_attribute_value');
    }
}
