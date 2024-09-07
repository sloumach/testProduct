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

        <!-- Sélection des attributs disponibles -->
        <h2>Sélectionner les attributs :</h2>
        @foreach ($attributes as $attribute)
            <label for="attribute_{{ $attribute->id }}">
                <input type="checkbox" name="attribute_ids[]" value="{{ $attribute->id }}">
                {{ $attribute->name }}
            </label>
            <br>
        @endforeach

        <button type="submit">Créer le Produit et Générer les Variations</button>
    </form>
    <hr>

    <h2>Ajouter des Attributs</h2>

<!-- Formulaire pour ajouter des attributs -->
<form action="{{ route('attributes.store') }}" method="POST">
    @csrf
    <label for="attribute_name">Nom de l'Attribut :</label>
    <input type="text" name="attribute_name" id="attribute_name" placeholder="Exemple: Taille, Couleur, etc." required>

    <label for="attribute_values">Valeurs de l'Attribut (séparées par des virgules) :</label>
    <input type="text" name="attribute_values" id="attribute_values" placeholder="Exemple: S, M, L ou Rouge, Bleu" required>

    <button type="submit">Ajouter l'Attribut</button>
</form>


    <hr>

    <h2>Attributs Disponibles</h2>

    <!-- Liste des attributs existants avec leurs valeurs -->
    <ul>
        @foreach ($attributes as $attribute)
            <li>{{ $attribute->name }} : {{ implode(', ', $attribute->values->pluck('value')->toArray()) }}</li>
        @endforeach
    </ul>
</body>
</html>
