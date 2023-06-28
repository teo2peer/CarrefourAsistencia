<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DomCrawler\Crawler;
use DOMDocument; 
use DOMXPath; 

class CarrefourCrawler
{
    public function searchProductByEAN($ean)
    {
        $googleUrl = 'https://www.google.com/search?q=' . urlencode($ean) . '+site:carrefour.es';
        
        $response = $this->makeCurlRequest($googleUrl);
        $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
        $response = strtr( $response, $unwanted_array );
        if ($response) {
            $doc = new DOMDocument();
            $doc->encoding = 'UTF-8';
            @$doc->loadHTML('<?xml encoding="UTF-8">' . $response);
            $finder = new DomXPath($doc);
            $a = $finder->query("//*[contains(@class, 'yuRUbf')]/a")->item(0);
            $carrefourUrl = $a->getAttribute('href');
        }else{
            return "No se ha encontrado el producto";
        }
            
        
        if ($carrefourUrl) {
            $response = $this->makeCurlRequest($carrefourUrl);
        
            if ($response) {
                $doc = new DOMDocument();
                $doc->encoding = 'utf-8';
                @$doc->loadHTML($response);
                $finder = new DomXPath($doc);
                // product name h1 class product-header__name
                // product image inside img class pics-slider__thumbnail-img
                // product price span class buybox__price
                $productName = $finder->query("//h1[contains(@class, 'product-header__name')]")->item(0)->nodeValue;
                $productImage = $finder->query("//img[contains(@class, 'pics-slider__thumbnail-img')]")->item(0)->getAttribute('src');
                $productPrice = $finder->query("//span[contains(@class, 'buybox__price')]")->item(0)->nodeValue;
                
                // reeplace 100x to 400x in image
                $productImage = str_replace('100x', '400x', $productImage);
                // trim all spaces
                $productName = trim($productName);
                $productPrice = trim($productPrice);
                $productImage = trim($productImage);
                // replace "\/" to "/"
                $productImage = str_replace('\/', '/', $productImage);
                // remove \n
                $productImage = str_replace("\n", "", $productImage);
                $productName = str_replace("\n", "", $productName);
                $productPrice = str_replace("\n", "", $productPrice);
            

                $tipo = $this->clasificarProducto($finder);
                $especificaciones = $this->especificaciones($finder, $tipo);

                return [
                    'name' => $productName,
                    'image' => $productImage,
                    'price' => $productPrice,
                    'tipo' => $tipo,
                    'especificaciones' => $especificaciones
                ];
            }
            
            return "No se ha encontrado el producto 2";            
        }
        
        return "Error general ";
    }
    


    
    private function makeCurlRequest($url)
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.4951.54 Safari/537.36');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_HTTPHEADER,array (
            "Content-Type: text/xml; charset=utf-8"
        ));

        
        $response = curl_exec($ch);
        
        curl_close($ch);
        
        return $response;
    }
    
    private function clasificarProducto($data){
        // search for breadcrumb and change all to text
        $breadcrumb = $data->query("//div[contains(@class, 'breadcrumb')]")->item(0)->nodeValue;
        // all type: Climatización, Frigoríficos, Lavadoras, Lavadoras-Secadoras, Secadoras, Lavavajillas, Microondas, Hornos, Aspiradoras, Móviles libres
        if (strpos($breadcrumb, 'Televisores TV') !== false) {
            return "TV";
        }else if (strpos($breadcrumb, 'Climatización') !== false) {
            return "Climatización";
        }else if ((strpos($breadcrumb, 'Frigoríficos') !== false || strpos($breadcrumb, 'FrigorÃ­fico')!== false || strpos($breadcrumb, 'Frigo')!== false ) && strpos($breadcrumb, 'Combi') !== false) {
            return "Neveras"; 
        }else if (strpos($breadcrumb, 'Congeladores') !== false) {
            return "Congeladores";
        }else if (strpos($breadcrumb, 'Lavadoras') !== false && strpos($breadcrumb, 'Lavasecadora') === false) {
            return "Lavadoras";
        }else if (strpos($breadcrumb, 'Lavasecadora') !== false) {
            return "Lavadoras-Secadoras";
        }else if (strpos($breadcrumb, 'Secadoras') !== false) {
            return "Secadoras";
        }else if (strpos($breadcrumb, 'Lavavajillas') !== false) {
            return "Lavavajillas";
        }else if (strpos($breadcrumb, 'Microondas') !== false) {
            return "Microondas";
        }else if (strpos($breadcrumb, 'Hornos') !== false) {
            return "Hornos";
        }else if (strpos($breadcrumb, 'Aspiradoras') !== false) {
            return "Aspiradoras";
        }else if (strpos($breadcrumb, 'Móviles libres') !== false) {
            return "SmartPhone";
        }else{
            return "Otros";
        }
    }

    private function especificaciones($data, $tipo){
        // de ambito general: Dimensiones del producto
        // formato: <dt class="product-details__content-title even"> Dimensiones del producto (AltoxAnchoXFondo)</dt> <dd class="product-details__content-value even">  88.3 x 146.2 x 24.9</dd>
        
        // buscar entre todas las caracteristicas
        // foreach product-details__section-contents 
        $productDetails = [];

        // Obtener la lista de características del producto
        $featureNodes = $data->query('//div[@class="product-details__feature-container"]');
        foreach ($featureNodes as $featureNode) {
            $featureList = $data->query('.//dl[@class="product-details__section-contents"]/dt', $featureNode);
            $featureValues = $data->query('.//dl[@class="product-details__section-contents"]/dd', $featureNode);
            $productDetails = $this->filterEspecificaciones($featureList, $featureValues, $tipo);
            

        }
        return $productDetails;
    }

    private function filterEspecificaciones($title, $value, $tipo){
        // valores a filtrar
        $frigo = ["Dimensiones del", "No Frost", "Congelador No Frost", "til total", "til frigorífico", "til congelador", "Nivel de ruido", "sn energ","mero de cajones"];
        $frigoKeys = ["Dimensiones", "No Frost", "Congelador No Frost", "Capacidad total", "Capacidad frigorífico", "Capacidad congelador", "Nivel de ruido", "Clasificación energética", "Número de cajones"];

        $tv = ["pulgadas", "o en cm","Tipo de pantalla", "Prestaciones de la Imagen", "Tipo de panel", "a Color", "Resolución", "HDR", "Brillo", "Tipo TV", "HDMI", "Wifi", "Procesador", "Tipo de sistema operativo", "nsiones del producto"];
        $tvKeys = ["Pulgadas", "Tamaño", "Tipo de pantalla", "Prestaciones de la Imagen", "Tipo de panel", "Tecnología Color", "Resolución", "HDR", "Brillo", "Tipo TV", "HDMI", "Wifi", "Procesador", "Sistema operativo", "Dimensiones del producto"];
        
        $lavadoras = ["Capacidad","ciencia de lavado","xima Centrifugado","Tipo de motor","n vapor","stica en lavado","Tipo de cuba", "Dimensiones del producto"];
        $lavadorasKeys = ["Capacidad","Eficiencia de lavado","Velocidad máxima Centrifugado","Tipo de motor","Función vapor","DB","Dimensiones del producto"];
        

        $lavadoras_secadoras = [];


        $res = [];


        if($tipo == "Neveras"){
        $arrayKey = $frigo;
        $arrayKeyName = $frigoKeys;
        }else if($tipo == "TV"){
        $arrayKey = $tv;
        $arrayKeyName = $tvKeys;
        }else if($tipo == "Lavadoras"){
        $arrayKey = $lavadoras;
        $arrayKeyName = $lavadorasKeys;
        }else{
            foreach ($title as $key => $value1) {
                        $res[$title[$key]->nodeValue] = str_replace("\n", "",trim($value[$key]->nodeValue));
            }
            return $res;
        }


        foreach ($title as $key => $value1) {
            // chekc like strpos($breadcrumb, 'Frigoríficos') !== false
            foreach ($arrayKey as $key2 => $value2) {
                if (strpos($value1->nodeValue, $value2) !== false) {
                    $res[$arrayKeyName[$key2]] = str_replace("\n", "",trim($value[$key]->nodeValue));
                }
            }
            
        }

        return $res;
    
    }   

}