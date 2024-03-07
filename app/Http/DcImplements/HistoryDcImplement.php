<?php

namespace App\Http\DcImplements;


class HistoryDcImplement
{
    /**
     * OBtener Historial completo
     * 
     * @param Illuminate\Support\Facades\DB $connection
     * @return array
     */
    public function getHistory($connection)
    {
        $array_history = $connection->select("SELECT * FROM `history`");
        $coleccion = collect($array_history);

        // Agrupar por el campo day_week
        $history = $coleccion->groupBy('name');

        return  $history;
    }

    /**
     * crear un registro para el historial
     * 
     * @param Illuminate\Support\Facades\DB $connection
     * @param string $name
     * @param float|integer $price
     * @param integer $day_week
     * @param string $statistics
     * @return array
     */
    public function createNewHistory(
        $connection,
        $name,
        $price,
        $day_week,
        $statistics
    ) {

        $data = [
            'name' => $name,
            'price' => $price,
            'day_week' => $day_week,
            'statistics' => $statistics,
        ];

        $connection->table('history')->insert($data);
    }
    /**
     * Eliminar todos los registros de una tabla
     * 
     * @param Illuminate\Support\Facades\DB $connection
     * @return mixed
     */
    public  static function deleteHistoryRecord($connection)
    {
        $connection->table('history')->truncate();
    }

    /**
     * Obtiene el registro según el dia de la semana 
     * 
     * @param Illuminate\Support\Facades\DB $connection
     * @param integer  $day_week dia de la semana 1 - 6 
     * @return array
     */
    public static function getDayWeekRate($connection, $day_week): array
    {
        return $connection->select("SELECT * FROM `history` WHERE day_week = :day_week", [
            "day_week" => $day_week
        ]);
    }

    /**
     * Buscar arreglo por nombre
     *
     * @return int
     */
    public static function searchBankForName($array, $name, $keyName = 'name')
    {
        foreach ($array  as $value) {
            $banco =  $value;

            //*si es un objecto stdClass lo pareamos a un arreglo asociativo
            if (is_object($banco) && $banco instanceof \stdClass)   $banco  = get_object_vars($value);

            if ($banco[$keyName] === $name)  return $value;
        }
    }

    /**
     * obtiene el estatus 
     * 
     * @return  string
     */
    public static function getStatusBankDb(string $before_status, string $today_status): string
    {
        if ($today_status > $before_status) {
            return 'alto';
        } else if ($today_status < $before_status) {
            return 'bajo';
        } else {
            return 'neutro';
        }
    }

    public static function getStatusBcvToday($connection, $day_week)
    {
        
        $list_history_today = self::getDayWeekRate($connection, $day_week);
        $list_history_before =  self::getDayWeekRate($connection, $day_week - 1);


        $BCV_today = (object) HistoryDcImplement::searchBankForName($list_history_today, 'BCV');
        $BCV_before = (object)  HistoryDcImplement::searchBankForName($list_history_before, 'BCV');
      
        return self::getStatusBankDb($BCV_before->price, $BCV_today->price);
    }
}
