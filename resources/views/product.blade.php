<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $product->name }}</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <h1>{{ $product->name }}</h1>
    <p>{{ $product->description }}</p>

    <!-- Boucle sur les attributs disponibles du produit -->
    @foreach ($attributes as $attribute)
        <label for="attribute_{{ $attribute->id }}">{{ $attribute->name }} :</label>
        <select name="attribute_{{ $attribute->id }}" id="attribute_{{ $attribute->id }}" class="attribute-select">
            <option value="" disabled selected>Choisir {{ $attribute->name }}</option>
            @foreach ($attribute->values as $value)
                <option value="{{ $value->id }}">{{ $value->value }}</option>
            @endforeach
        </select>
        <br>
    @endforeach



    <!-- Affichage du prix -->
    <h3>Prix: <span id="price">{{ $product->base_price }}</span> €</h3>

</body>

<script>
    $(document).ready(function() {
        // Configure AJAX pour inclure le token CSRF dans les en-têtes
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Quand l'utilisateur change un attribut
        $('.attribute-select').on('change', function() {
            updatePrice();
        });

        function updatePrice() {
            console.log('1: Commence le processus d\'ajustement de prix');
            var selectedAttributes = {};

            // Parcourir tous les attributs sélectionnés et collecter leurs valeurs
            $('.attribute-select').each(function() {
                var attributeId = $(this).attr('id').replace('attribute_', '');
                var valueId = $(this).val();

                // Ajouter l'attribut s'il est sélectionné
                if (valueId) {
                    selectedAttributes[attributeId] = valueId;
                }
            });

            console.log('2: Attributs sélectionnés:', selectedAttributes);

            // Si au moins un attribut a été sélectionné
            if (Object.keys(selectedAttributes).length > 0) {
                console.log('3: Attributs disponibles sélectionnés, appel AJAX');

                $.ajax({
                    url: "{{ route('product.price') }}", // Route pour récupérer le prix via AJAX
                    method: 'POST',
                    data: {
                        product_id: {{ $product->id }},
                        selected_attributes: selectedAttributes
                    },
                    beforeSend: function() {
                        console.log('Requête AJAX en cours...');
                    },
                    success: function(response) {
                        console.log('4: Réponse reçue', response); // Affichez la réponse complète
                        if (response.price) {
                            $('#price').text(response.price); // Mise à jour du prix
                        } else {
                            $('#price').text('Non disponible');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('5: Erreur dans la requête AJAX');
                        console.log("Statut: ", status);
                        console.log("Erreur: ", error);
                        console.log("Réponse: ", xhr
                        .responseText); // Affiche la réponse d'erreur complète
                        alert('Erreur lors de la récupération du prix.');
                    }
                });

            } else {
                console.log('3: Aucun attribut sélectionné, aucun appel AJAX');
            }
        }
    });
</script>

</html>
