<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Produit</title>
</head>
<body>
    <h1>Créer un Produit</h1>

    <form action="{{ route('product.store') }}" method="POST">
        @csrf
        <!-- Détails du produit -->
        <label for="name">Nom du Produit :</label>
        <input type="text" name="name" id="name" required>

        <label for="description">Description :</label>
        <textarea name="description" id="description" required></textarea>

        <label for="base_price">Prix de Base :</label>
        <input type="number" name="base_price" id="base_price" required step="0.01">

        <button type="submit">Créer le Produit et Générer les Variations</button>
    </form>
</body>
</html>
