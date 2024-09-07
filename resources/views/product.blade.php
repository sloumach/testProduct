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
            var selectedAttributes = {};

            // Parcourir tous les attributs sélectionnés et collecter leurs valeurs
            $('.attribute-select').each(function() {
                var attributeId = $(this).attr('id').replace('attribute_', '');
                var valueId = $(this).val();
                if (valueId) {
                    selectedAttributes[attributeId] = valueId;
                }
            });


            if (Object.keys(selectedAttributes).length === $('.attribute-select').length) {
                $.ajax({
                    url: "{{ route('product.price') }}",
                    method: 'POST',
                    data: {
                        product_id: {{ $product->id }},
                        selected_attributes: selectedAttributes
                    },
                    success: function(response) {
                        if (response.price) {
                            $('#price').text(response.price);
                        } else {
                            $('#price').text('Non disponible');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert("Erreur: " + xhr.responseText); // Afficher l'erreur dans une alerte
                    }

                });
            } else {
                alert('Tous les attributs ne sont pas encore sélectionnés');
            }
        }

    });
</script>

</html>
