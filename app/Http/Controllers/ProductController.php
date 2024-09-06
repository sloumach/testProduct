<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\AttributeValue;
use App\Models\ProductVariation;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Afficher le produit et ses attributs
    public function show($id)
    {
        $product = Product::findOrFail($id);

        // Récupérer les tailles et couleurs disponibles pour ce produit
        $sizes = AttributeValue::whereHas('attribute', function($query) {
            $query->where('name', 'Taille');
        })->get();

        $colors = AttributeValue::whereHas('attribute', function($query) {
            $query->where('name', 'Couleur');
        })->get();

        return view('product', compact('product', 'sizes', 'colors'));
    }

    // Récupérer le prix de la variation en fonction des attributs sélectionnés
    public function getPrice(Request $request)
    {
        // Cherche la variation de produit correspondant à la taille ET à la couleur
        $productVariation = ProductVariation::where('product_id', $request->product_id)
            ->whereHas('attributes', function($query) use ($request) {
                // Vérifie la taille
                $query->where('attribute_value_id', $request->size_id)
                      ->whereHas('attribute', function ($q) {
                          $q->where('name', 'Taille');
                      });
            })
            ->whereHas('attributes', function($query) use ($request) {
                // Vérifie la couleur
                $query->where('attribute_value_id', $request->color_id)
                      ->whereHas('attribute', function ($q) {
                          $q->where('name', 'Couleur');
                      });
            })
            ->first();

        // Si la variation est trouvée, retourner son prix
        if ($productVariation) {
            return response()->json(['price' => $productVariation->price]);
        }

        // Si aucune variation n'est trouvée, retourner une erreur
        return response()->json(['message' => 'Variation non trouvée', 'price' => null], 404);
    }






}
