<?php

namespace App\Http\DcImplements;
//use App\Http\DcImplements\HistoryDcImplement;

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
        return $connection->select("SELECT * FROM `history`");
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
}
