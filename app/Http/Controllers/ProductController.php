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
        // Validation des données envoyées par l'AJAX
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'size_id' => 'required|integer|exists:attribute_values,id',
            'color_id' => 'required|integer|exists:attribute_values,id',
        ]);
        $price = $this->priceService->getProductPrice(
            $validated['product_id'],
            $validated['size_id'],
            $validated['color_id']
        );


        // Si la variation est trouvée, retourner son prix
        if ($price) {
            return response()->json(['price' => $price]);
        }

        // Si aucune variation n'est trouvée, retourner une erreur
        return response()->json(['message' => 'Variation non trouvée', 'price' => null], 404);
    }

    public function create()
    {
        // Récupérer les tailles et les couleurs disponibles
        $sizes = AttributeValue::whereHas('attribute', function($query) {
            $query->where('name', 'Taille');
        })->get();

        $colors = AttributeValue::whereHas('attribute', function($query) {
            $query->where('name', 'Couleur');
        })->get();

        return view('product.create', compact('sizes', 'colors'));
    }

    /**
     * Enregistre le produit et génère les variations.
     */
    public function store(Request $request)
{
    // Validation des données de base
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'base_price' => 'required|numeric|min:0',
    ]);

    // Créer le produit
    $product = Product::create([
        'name' => $validated['name'],
        'description' => $validated['description'],
        'base_price' => $validated['base_price'],
    ]);

    // Récupérer les tailles et les couleurs disponibles
    $sizes = AttributeValue::whereHas('attribute', function($query) {
        $query->where('name', 'Taille');
    })->get();

    $colors = AttributeValue::whereHas('attribute', function($query) {
        $query->where('name', 'Couleur');
    })->get();

    // Générer les variations avec des prix différents selon les tailles et couleurs
    foreach ($sizes as $size) {
        foreach ($colors as $color) {
            // Logique pour ajuster le prix en fonction de la taille et de la couleur
            $priceAdjustment = 0;

            // Exemple : ajustement selon la taille
            if ($size->value == 'S') {
                $priceAdjustment = -2; // Réduction pour petite taille
            } elseif ($size->value == 'M') {
                $priceAdjustment = 0; // Pas de changement pour taille moyenne
            } elseif ($size->value == 'L') {
                $priceAdjustment = 3; // Augmentation pour grande taille
            }

            // Exemple : ajustement selon la couleur
            if ($color->value == 'Rouge') {
                $priceAdjustment += 1; // Augmentation pour couleur rouge
            } elseif ($color->value == 'Bleu') {
                $priceAdjustment += 2; // Augmentation pour couleur bleue
            }

            // Calculer le prix final de la variation
            $finalPrice = $validated['base_price'] + $priceAdjustment;

            // Créer la variation
            $productVariation = ProductVariation::create([
                'product_id' => $product->id,
                'price' => $finalPrice, // Prix ajusté pour chaque variation
                'stock' => 10, // Stock par défaut, peut être ajusté
            ]);

            // Associer les attributs (taille et couleur) à la variation
            $productVariation->attributes()->attach([$size->id, $color->id]);
        }
    }

    return redirect()->route('product.create')->with('success', 'Produit et variations créés avec succès !');
}







}
