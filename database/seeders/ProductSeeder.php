<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductVariation;

class ProductSeeder extends Seeder
{
    public function run()
{
    // Créer un produit
    $product = Product::create([
        'name' => 'Maillot de Football',
        'description' => 'Un maillot de football confortable et durable.',
        'base_price' => 50.00,
    ]);

    // Créer des attributs (taille et couleur)
    $size = Attribute::create(['name' => 'Taille']);
    $color = Attribute::create(['name' => 'Couleur']);

    // Créer les valeurs pour chaque attribut (Taille et Couleur)
    $small = AttributeValue::create(['attribute_id' => $size->id, 'value' => 'S']);
    $medium = AttributeValue::create(['attribute_id' => $size->id, 'value' => 'M']);
    $large = AttributeValue::create(['attribute_id' => $size->id, 'value' => 'L']);

    $red = AttributeValue::create(['attribute_id' => $color->id, 'value' => 'Rouge']);
    $blue = AttributeValue::create(['attribute_id' => $color->id, 'value' => 'Bleu']);

    // Créer des variations de produit avec prix pour chaque combinaison de taille et de couleur
    // Taille S, Couleur Rouge
    $variation1 = ProductVariation::create([
        'product_id' => $product->id,
        'price' => 60.00,
        'stock' => 10
    ]);
    $variation1->attributes()->attach([$small->id, $red->id]);

    // Taille S, Couleur Bleu
    $variation2 = ProductVariation::create([
        'product_id' => $product->id,
        'price' => 65.00,
        'stock' => 8
    ]);
    $variation2->attributes()->attach([$small->id, $blue->id]);

    // Taille M, Couleur Rouge
    $variation3 = ProductVariation::create([
        'product_id' => $product->id,
        'price' => 70.00,
        'stock' => 15
    ]);
    $variation3->attributes()->attach([$medium->id, $red->id]);

    // Taille M, Couleur Bleu
    $variation4 = ProductVariation::create([
        'product_id' => $product->id,
        'price' => 75.00,
        'stock' => 12
    ]);
    $variation4->attributes()->attach([$medium->id, $blue->id]);

    // Taille L, Couleur Rouge
    $variation5 = ProductVariation::create([
        'product_id' => $product->id,
        'price' => 80.00,
        'stock' => 5
    ]);
    $variation5->attributes()->attach([$large->id, $red->id]);

    // Taille L, Couleur Bleu
    $variation6 = ProductVariation::create([
        'product_id' => $product->id,
        'price' => 85.00,
        'stock' => 3
    ]);
    $variation6->attributes()->attach([$large->id, $blue->id]);
}

}
