<?php

namespace App\Services;
use App\Models\ProductVariation;


class HelperService
{
    /**
     * Génère toutes les combinaisons possibles des attributs.
     *
     * @param array $arrays
     * @return array
     */
    public static function generateCombinations($arrays)
    {
        $result = [[]];
        foreach ($arrays as $property => $propertyValues) {
            $temp = [];
            foreach ($result as $resultItem) {
                foreach ($propertyValues as $propertyValue) {
                    $temp[] = array_merge($resultItem, [$propertyValue]);
                }
            }
            $result = $temp;
        }
        return $result;
    }

    /**
     * Calcule le prix final en fonction de la combinaison d'attributs.
     *
     * @param float $basePrice
     * @param array $combination
     * @return float
     */
    public static function calculatePrice($basePrice, $combination)
    {
        $priceAdjustment = 0;

        // Parcourir tous les attributs de la combinaison et ajuster le prix aléatoirement
        foreach ($combination as $attributeValue) {
            // Générer un ajustement de prix aléatoire pour chaque attribut
            $randomAdjustment = rand(-5, 10); // Par exemple, ajustement aléatoire entre -5€ et +10€
            $priceAdjustment += $randomAdjustment;
        }

        // Retourner le prix de base ajusté
        return $basePrice + $priceAdjustment;
    }
    public static function getPriceForSelectedAttributes($productId, $selectedAttributes)
    {
        // Récupérer toutes les variations de produit correspondant à cet ID de produit
        $productVariations = ProductVariation::where('product_id', $productId)->get();

        // Parcourir toutes les variations pour trouver celle qui correspond exactement aux attributs sélectionnés
        foreach ($productVariations as $variation) {
            // Récupérer les IDs des valeurs d'attributs pour cette variation
            $variationAttributeValues = $variation->attributes()->pluck('attribute_value_id')->toArray();

            // Comparer les valeurs d'attributs de la variation avec celles sélectionnées par l'utilisateur
            if (empty(array_diff($variationAttributeValues, $selectedAttributes)) && count($variationAttributeValues) === count($selectedAttributes)) {
                // Si la variation correspond exactement aux attributs sélectionnés, retourner son prix
                return $variation->price;
            }
        }

        // Si aucune variation ne correspond, retourner null
        return null;
    }

}
