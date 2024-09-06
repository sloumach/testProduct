<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\AttributeValue;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use App\Services\PriceService;

class ProductController extends Controller
{
    protected $priceService;

    public function __construct(PriceService $priceService)
    {
        $this->priceService = $priceService;
    }
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
        // Validation des données
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'size_id' => 'required|integer|exists:attribute_values,id',
            'color_id' => 'required|integer|exists:attribute_values,id',
        ]);

        // Utilisation du service pour obtenir le prix
        $price = $this->priceService->getProductPrice(
            $validated['product_id'],
            $validated['size_id'],
            $validated['color_id']
        );

        // Si le prix est trouvé, le retourner
        if ($price !== null) {
            return response()->json(['price' => $price]);
        }

        // Si aucune variation n'est trouvée, retourner un message d'erreur
        return response()->json(['message' => 'Variation non trouvée', 'price' => null], 404);
    }






}
