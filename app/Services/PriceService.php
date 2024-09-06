<?php

namespace App\Services;

use App\Models\ProductVariation;

class PriceService
{
    /**
     * Obtenir le prix d'une variation de produit selon la taille et la couleur.
     *
     * @param int $productId
     * @param int $sizeId
     * @param int $colorId
     * @return float|null
     */
    public function getProductPrice(int $productId, int $sizeId, int $colorId): ?float
    {
        // Cherche la variation de produit correspondant à la taille ET à la couleur
        $productVariation = ProductVariation::where('product_id', $productId)
            ->whereHas('attributes', function($query) use ($sizeId) {
                // Vérifie la taille
                $query->where('attribute_value_id', $sizeId);
            })
            ->whereHas('attributes', function($query) use ($colorId) {
                // Vérifie la couleur
                $query->where('attribute_value_id', $colorId);
            })
            ->first();

        // Si une variation est trouvée, retourner son prix
        return $productVariation ? $productVariation->price : null;
    }
}
