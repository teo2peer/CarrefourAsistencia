<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;
use App\Http\Controllers\CarrefourCrawler;

class ProductoController extends Controller
{
    public function agregarProducto()
    {
        return view('agregar_producto');
    }

    public function agregarBuscar(Request $request)
    {
        $ean = $request->input('ean');

         // Crear una instancia de CarrefourCrawler y buscar el producto
        $carrefourCrawler = new CarrefourCrawler();
        $response = $carrefourCrawler->searchProductByEAN($ean);

        return $response;
    }
}