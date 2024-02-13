<?php

namespace App\Http\DcImplements;
//use App\Http\DcImplements\HistoryDcImplement;

class HistoryDcImplement
{
    /**
     * @param Illuminate\Support\Facades\DB $connection
     * @return array
     */
    public function getHistory($connection){
        return $connection->select("SELECT * FROM `history`");
    }
}
