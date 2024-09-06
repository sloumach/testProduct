<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = ['name'];

    // Un attribut a plusieurs valeurs (ex: "Taille" a les valeurs "S", "M", "L")
    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }
}
