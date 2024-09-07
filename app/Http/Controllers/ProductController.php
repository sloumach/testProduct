<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\AttributeValue;
use App\Models\Attribute;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use App\Services\HelperService;
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
        // Récupérer le produit par son ID
        $product = Product::findOrFail($id);

        // Récupérer tous les attributs et leurs valeurs disponibles pour ce produit
        $attributes = Attribute::with(['values' => function ($query) use ($product) {
            $query->whereHas('productVariations', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            });
        }])->get();

        // Retourner la vue avec le produit et les attributs disponibles
        return view('product', compact('product', 'attributes'));
    }

    // Récupérer le prix de la variation en fonction des attributs sélectionnés
    public function getPrice(Request $request)
    {
        // Récupérer les attributs sélectionnés par l'utilisateur
        $selectedAttributes = $request->input('selected_attributes', []);

        // Si aucun attribut n'est sélectionné, retournez une erreur
        if (empty($selectedAttributes)) {
            return response()->json(['message' => 'Aucun attribut sélectionné', 'price' => null], 400);
        }

        // Appel à la méthode du Helper pour obtenir le prix de la variation correspondante
        $price = HelperService::getPriceForSelectedAttributes($request->product_id, $selectedAttributes);

        // Si la variation est trouvée, retourner son prix
        if ($price !== null) {
            return response()->json(['price' => $price]);
        }

        // Si aucune variation ne correspond, retourner une erreur
        return response()->json(['message' => 'Variation non trouvée', 'price' => null], 404);
    }







    public function create()
    {
        // Récupérer les attributs disponibles
        $attributes = Attribute::with('values')->get();

        return view('product.create', compact('attributes'));
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
            'attribute_ids' => 'required|array', // Valider que des attributs sont sélectionnés
        ]);

        // Créer le produit
        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'base_price' => $validated['base_price'],
        ]);

        // Récupérer toutes les valeurs des attributs sélectionnés
        $selectedAttributes = AttributeValue::whereIn('attribute_id', $validated['attribute_ids'])->get();

        // Organiser les attributs par leur nom
        $groupedAttributes = $selectedAttributes->groupBy('attribute.name');

        // Générer les combinaisons d'attributs sélectionnés
        $combinations = HelperService::generateCombinations($groupedAttributes->toArray());

        // Créer les variations pour chaque combinaison
        foreach ($combinations as $combination) {
            $finalPrice = HelperService::calculatePrice($validated['base_price'], $combination);

            // Créer la variation de produit
            $productVariation = ProductVariation::create([
                'product_id' => $product->id,
                'price' => $finalPrice,
                'stock' => 10, // Stock par défaut, ajustable
            ]);

            // Attacher les attributs à la variation
            $productVariation->attributes()->attach(array_column($combination, 'id'));
        }

        return redirect()->route('product.create')->with('success', 'Produit et variations créés avec succès !');
    }


    public function storeAttributes(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'attribute_name' => 'required|string|max:255',
            'attribute_values' => 'required|string'
        ]);

        // Créer un nouvel attribut
        $attribute = Attribute::create(['name' => $validated['attribute_name']]);

        // Ajouter les valeurs associées
        $values = explode(',', $validated['attribute_values']); // Divise les valeurs séparées par des virgules
        foreach ($values as $value) {
            AttributeValue::create([
                'attribute_id' => $attribute->id,
                'value' => trim($value) // Trim pour supprimer les espaces inutiles
            ]);
        }

        return redirect()->route('product.create')->with('success', 'Attribut et valeurs ajoutés avec succès !');
    }





}
