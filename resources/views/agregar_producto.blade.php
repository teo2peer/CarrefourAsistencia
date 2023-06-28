<!DOCTYPE html>
<html>

<head>
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    {{-- laravel csfr --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    <div class="container">
        <h1 class="mt-5 mb-4">Buscar Producto</h1>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div id="qrScanner"></div>
                        <div class="input-group">
                            <input type="text" id="ean" name="ean" class="form-control"
                                placeholder="Introduce el EAN del producto">
                            <div class="input-group-append">
                                <button id="submit" name="submit" type="submit"
                                    class="btn btn-primary">Buscar</button>
                            </div>
                            <div class="input-group-append">
                                <button id="scanBtn" class="btn btn-primary">Escanear</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div id="productInfo" style="display: none;">
                            <h3 id="productName"></h2>
                            <h4 id="productPrice" style="color:red"></h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Especificacion</th>
                                        <th>Valor</th>

                                    </tr>
                                </thead>
                                <tbody id="productTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode@2.0.9/dist/html5-qrcode.min.js"></script>
    <script src="/assets/js/agregar_producto.js"></script>

</body>

</html>
