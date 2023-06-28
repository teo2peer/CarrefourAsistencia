let scanner;

// Mostrar el lector de códigos de barras al hacer clic en el botón de escanear
$('#scanBtn').click(function () {
    if (!scanner) {
        // Crear una instancia del escáner html5QrcodeScanner

        scanner = new Html5QrcodeScanner(
            "qrScanner",
            { fps: 10, qrbox: 250 },
            function (qrCode, decodedText, _element) {
                // Rellenar el campo EAN y enviar el formulario al escanear el código
                $('#ean').val(decodedText);
                $('#searchForm').submit();
            }
        );
    }

    $('#qrScanner').show();
    $('#scanBtn').hide();
    $('#searchForm').hide();

    // Iniciar el escáner html5QrcodeScanner
    scanner.render();
});

// Enviar la solicitud AJAX al controlador para buscar el producto
$('#submit').click(function () {
    $.ajax({
        url: "/agregar-buscar-producto",
        method: "POST",
        data: { ean: $('#ean').val(), _token: $('meta[name="csrf-token"]').attr('content') },
        success: function (response) {
            // clear table
            $('#productTableBody').empty();


            var product = response;
            var specifications = product.especificaciones;

            // Crear la fila de la tabla con los datos del producto
            $("#productName").text(product.name);

            var price = product.price;
            // remove from price all that is not number

            price = price.replace(/[^0-9]/g, '');

            $("#productPrice").text(price + " €");


            // Crear la fila de la tabla para las especificaciones del producto
            $.each(specifications, function (key, value) {
                var specRow = '<tr>' +
                    '<td>' + key + '</td>' +
                    '<td>' + value + '</td>' +
                    '</tr>';

                // Agregar la fila de especificaciones a la tabla
                $('#productTableBody').append(specRow);
            });

            $('#productInfo').show();
        },
        error: function (xhr, status, error) {
            console.error(error);
        }
    });
});
