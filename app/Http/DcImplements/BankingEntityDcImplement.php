<?php

namespace App\Http\DcImplements;

use Illuminate\Support\Facades\Http;
use DOMDocument;
use DOMXPath;

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
    public  function getBcv()
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
                    "name" => trim($this->getTagHtml('span', $div))
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
    public static function getRateList()
    {
        try {
            // Realiza la solicitud HTTP utilizando Http::get()
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Accept' => 'text/html',
                    'Accept-Charset' => 'utf-8'
                ])
                ->get('https://exchangemonitor.net/dolar-venezuela');

            // Verifica si hay errores
            if ($response->failed())   throw new \Exception('Error en la solicitud HTTP: ' . $response->status());

            libxml_use_internal_errors(true);

            //  Convertir el contenido a UTF-8  
            $html = utf8_decode($response->body());

            $dom = new DOMDocument();
            $dom->loadHTML($html);
            $xpath = new DOMXPath($dom);

            // Array para almacenar los bancos
            $bancos = [];

            // Obtener todos los elementos div
            $divs = $dom->getElementsByTagName('div');

            foreach ($divs as $div) {
                if ($div->hasAttribute('itemprop') && $div->getAttribute('itemprop') === 'itemListElement') {
                    $banco = [];


                    //descendiente de cualquier nivel del contexto actual que tenga el atributo itemprop 
                    $nombre = $xpath->evaluate('string(.//h6[@itemprop="name"])', $div);
                    $precio = $xpath->evaluate('string(.//p[@itemprop="price"])', $div);
                    $status = $xpath->evaluate('string(.//p[@class="cambio-por"])', $div);
                    $date = $xpath->evaluate('string(.//p[@class="fecha"])', $div);
                    $style = $xpath->evaluate('string(.//p[@class="cambio-por"]/@style)', $div);


                    if (!empty($nombre) || !empty($precio)) {

                        $nombre_convertido = iconv('UTF-8', 'ASCII//TRANSLIT', $nombre);
                        $key = str_replace(' ', '_', strtolower($nombre_convertido));

                        if ($key == "d'olar_em") $key =  "dolar_em";

                        $banco = [
                            'name' => $nombre,
                            'key' => $key,
                            'price' => $precio,
                            'percentage' => (string)  str_replace(['?', ' '], '', $status),
                            'label_status'  => $this->getStatusBank($style),
                            'date' =>    $this->goBackDate($date),
                            'date_label' => $date
                        ];

                        $bancos[] = $banco;
                    }
                }
            }

            return $bancos;
        } catch (\Exception $e) {
            // Maneja cualquier error que ocurra durante el proceso
            return 'Error: ' . $e->getMessage();
        }
    }

    /**
     * Retrocede una fecha segun un texto 
     * 
     * @param string $date_in_string texto texto fecha
     * @example  s Actualizó hace 9 horas
     * @return  DateTime
     */
    function goBackDate(string $date_in_string)
    {

        $fecha_actual = new \DateTime();
        //Actualizó hace 8 días
        //Actualizó hace más de un mes
        $units_of_time = [
            "horas" => 'hours',
            "hora" => 'hours',
            "segundos" => 'seconds',
            "minuto" => 'minutes',
            "minutos"  => 'minutes',
            "días" => 'days',
            "día" => 'days',
            "un" => 1,
            "una" => 1,
        ];

        //*trasformamos la palabra en un arreglo
        $date_in_array = explode(" ", $date_in_string);

        //*este es el caso de ser una palabra diferente elimina las sobras
        //!ejemplo Actualizó hace más de un mes 
        if (count($date_in_array) == 5) {
            unset($date_in_array[2]);
            unset($date_in_array[3]);
        }

        //!TERRMINAR ESTE BETA MANO // nueva frase
        $number = $date_in_array[2];

        //*evaluar caracteres especiales 
        $number =  empty($units_of_time[$number]) ?  $number  : 1;

        $time = $date_in_array[3];

        $value_date = '-' .  $number . ' ' . $units_of_time[$time];

        $fecha_actual->modify($value_date);

        return  $fecha_actual->format('Y-m-d H:i:s');
    }

    /**
     * obtener status de peticion
     * 
     * @param string style_string stylo
     * @example color:var(--color-verde) 
     * @return  string
     */
    function getStatusBank(string $style_string)
    {
        $status = [
            "verde" => "bajo", //subio
            "rojo" => "alto", // bajo
            "neutro" => "neutro"  //neutro
        ];
        preg_match('/--color-(verde|rojo|neutro)/', $style_string, $matches);
        return  $status[$matches[1]];
    }       
}
