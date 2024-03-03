<?php

namespace App\Http\DcImplements;


class AppConfigDailyDcImplement
{
    /**
     * obtiene una version
     * 
     * @param Illuminate\Support\Facades\DB $connection
     * @return null
     */
    public function getVersion($connection)
    {
        $version = $connection->selectOne("SELECT version FROM `config_app`")->version;

        return  [
            "version" => $version
        ];
    }
}
