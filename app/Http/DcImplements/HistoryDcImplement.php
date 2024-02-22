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
    public function getHistory($connection): array
    {
        $array_history = $connection->select("SELECT * FROM `history`");

        $coleccion = collect($array_history);

        // Agrupar por el campo day_week
        $agrupados = $coleccion->groupBy('day_week');

        // Crear un nuevo arreglo para almacenar los resultados agrupados
        $history = [];

        // Iterar sobre los grupos
        foreach ($agrupados as  $grupos) {
            $item = [];

            foreach ($grupos as $grupo) {

                $item[] = [
                    "name" => $grupo->name,
                    "price" => $grupo->price,
                    "statistics" => $grupo->statistics
                ];
            }

            $history[] = [
                'day' => $item,
                'date' => $grupos->pluck('created_at')->toArray()[0]
            ];
        }
        return $history;
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
    public function deleteHistoryRecord($connection)
    {
        $connection->table('history')->truncate();
    }

    /**
     * OBtener tasa del ultimo dia de la semana 
     * 
     * @param Illuminate\Support\Facades\DB $connection
     * @param integer  $day_week dia de la semana 1 - 6 
     * @return array
     */
    public function getDayWeekRate($connection, $day_week): array
    {
        return $connection->select("SELECT * FROM `history` WHERE day_week = :day_week", [
            "day_week" => $day_week
        ]);
    }
}
