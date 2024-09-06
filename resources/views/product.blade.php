<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Vos autres balises -->
    </head>

    <title>{{ $product->name }}</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>{{ $product->name }}</h1>
    <p>{{ $product->description }}</p>

    <!-- Sélection de la taille -->
    <label for="size">Taille:</label>
    <select name="size" id="size">
        <option value="" disabled selected>Choisir la taille</option>
        @foreach ($sizes as $size)
            <option value="{{ $size->id }}">{{ $size->value }}</option>
        @endforeach
    </select>

    <!-- Sélection de la couleur -->
    <label for="color">Couleur:</label>
    <select name="color" id="color" disabled>
        <option value="" disabled selected>Choisir la couleur</option>
        @foreach ($colors as $color)
            <option value="{{ $color->id }}">{{ $color->value }}</option>
        @endforeach
    </select>

    <!-- Affichage du prix -->
    <h3>Prix: <span id="price">{{ $product->base_price }}</span> €</h3>

    <script>
$(document).ready(function() {
    // Configure AJAX pour inclure le token CSRF dans les en-têtes
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Lors du changement de taille
    $('#size').on('change', function() {
        $('#color').prop('disabled', false);
        updatePrice();
    });

    // Lors du changement de couleur
    $('#color').on('change', function() {
        updatePrice();
    });

    function updatePrice() {
        var sizeId = $('#size').val();
        var colorId = $('#color').val();
        console.log("Taille ID: " + sizeId + ", Couleur ID: " + colorId); // Vérifiez les valeurs envoyées

        if (sizeId && colorId) {
            $.ajax({
                url: "{{ route('product.price') }}", // Route pour récupérer le prix via AJAX
                method: 'POST',  // Utilisation de GET pour récupérer les données
                data: {
                    size_id: sizeId,
                    color_id: colorId,
                    product_id: {{ $product->id }}
                },
                success: function(response) {
                    console.log("Réponse reçue: ", response); // Ajout de console.log pour vérifier la réponse
                    if (response.price) {
                        $('#price').text(response.price); // Mise à jour du prix
                    } else {
                        $('#price').text('Non disponible');
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Erreur: ", xhr.responseText); // Afficher les erreurs de requête
                    alert('Erreur lors de la récupération du prix.');
                }
            });
        }
    }
});


    </script>
</body>
</html>
