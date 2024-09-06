<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariationAttributeValue extends Pivot
{
    protected $table = 'product_variation_attribute_values'; // Assurez-vous que le nom correspond à celui de la migration


    protected $fillable = [
        'product_variation_id',
        'attribute_value_id',
    ];

    // Relation vers la variation de produit
    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class);
    }

    // Relation vers la valeur de l'attribut
    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }
}
