<?php

namespace App\Http\DcImplements;

use Illuminate\Support\Facades\Http;
use DOMDocument;

const meses = [
    "enero" => 1,
    "febrero" => 2,
    "marzo" => 3,
    "abril" => 4,
    "mayo" => 5,
    "junio" => 6,
    "julio" => 7,
    "agosto" => 8,
    "septiembre" => 9,
    "octubre" => 10,
    "noviembre" => 11,
    "diciembre" => 12
];

class BankingEntityDcImplement
{
    /**
     *  Obtiene las tasas del banco actualizadas
     * 
     * @return array
     */
    public function getBcv()
    {
        $response = Http::withoutVerifying()
            ->withHeaders(['Accept' => 'text/html'])
            ->get('https://www.bcv.org.ve/');

        if ($response->ok()) {
            $html = $response->body();

            // Saltamos errores de sintaxis
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();

            //Cargamos html de la petición
            $dom->loadHTML($html);
            // Saltamos errores de sintaxis
            libxml_use_internal_errors(false);

            //Capturamos los divs
            $divs = $dom->getElementsByTagName('div');
            $div_euro = $dom->saveHTML($dom->getElementById('euro'));
            $div_dolar = $dom->saveHTML($dom->getElementById('dolar'));
            $div_try =  $dom->saveHTML($dom->getElementById('lira'));
            $div_cny =  $dom->saveHTML($dom->getElementById('yuan'));
            $div_rub =  $dom->saveHTML($dom->getElementById('rublo'));

            $dateToday = null;

            //capturamos la fecha del html
            foreach ($divs as $div) {
                $clase = $div->getAttribute('class');
                if ($clase == 'pull-right dinpro center') {
                    $dateToday = $div->nodeValue;
                }
            }

            $div_htmls = [$div_euro, $div_dolar, $div_try, $div_cny, $div_rub];
            $money = [];

            foreach ($div_htmls  as $key => $div) {

                $text = trim($this->getTagHtml('span', $div));

                $money[] = [
                    "price" => trim($this->getTagHtml('strong', $div)),
                    "iso" => trim($this->getTagHtml('span', $div))
                ];
            }

            //Limpiamos valores de la fecha
            $dateToday = trim(substr($dateToday, strpos($dateToday, ":") + 2));

            //creamos un arreglo para la fecha
            $arrayDateToday = explode(" ", $dateToday);

            //si hay coincidencia creamos la fecha
            foreach (meses as $key => $mes) {
                if ($key === strtolower($arrayDateToday[2])) {
                    $arrayDateToday[2] = $mes;
                    $dateToday = $arrayDateToday[4] . '-' . $arrayDateToday[2] . '-' . $arrayDateToday[1];
                }
            }

            return  [
                "date" => $dateToday,
                "currency" => $money
            ];
        }
    }

    /**
     *Obtiene un label dentro de una etiqueta
     *
     * @param string $tag
     * @param string $html
     * 
     * @return string
     * 
     */
    function getTagHtml($tag, $html)
    {
        $startTag = '<' . $tag . '>';
        $endTag = '</' . $tag . '>';

        $start = strpos($html, $startTag);
        $end = strpos($html, $endTag);

        if ($start !== false && $end !== false) {
            $start += strlen($startTag);
            $length = $end - $start;

            return substr($html, $start, $length);
        }

        return null;
    }

    /**
     * Obtiene la tasa de enparalelo
     * 
     * @return array
     * 
     */
    function getEnParalelo()
    {
        $response = Http::withoutVerifying()
            ->withHeaders(['Accept' => 'text/html'])
            ->get('https://monitordolarvenezuela.com/');


        $html = $response->body();

        // Saltamos errores de sintaxis
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();

        //Cargamos html de la petición
        $dom->loadHTML($html);
        // Saltamos errores de sintaxis
        
        // Obtener todas las etiquetas <small>
        $smallTags = $dom->getElementsByTagName('small');
        
        foreach ($smallTags as $smallTag) {
            // Imprimir el contenido de cada etiqueta <small>
            dump( $smallTag->nodeValue . "\n");
        }
        libxml_use_internal_errors(false);
    }
}
