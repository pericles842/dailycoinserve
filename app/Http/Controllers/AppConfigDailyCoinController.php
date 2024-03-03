<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\DcImplements\AppConfigDailyDcImplement;

class AppConfigDailyCoinController extends Controller
{
    private $appConfigDailyDcImplement;


    public function __construct(AppConfigDailyDcImplement $appConfigDailyDcImplement)
    {
        $this->appConfigDailyDcImplement = $appConfigDailyDcImplement;
    }

    /**
     *Obtener version
     * 
     * @param Illuminate\Support\Facades\DB $connection
     * @return string
     */
    public function getVersion()
    {
        try {

            $data = $this->appConfigDailyDcImplement->getVersion(DB::connection());
        } catch (\Exception $e) {
            return $e;
        }

        return response($data, 200)->header('Content-Type', 'application/json');
    }
}
