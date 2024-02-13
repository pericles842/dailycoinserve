<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Constant;
use App\Http\DcImplements\HistoryDcImplement;

class HistoryController extends Controller
{
    private $historyDcImplement;

    public function __construct(HistoryDcImplement $historyDcImplement)
    {
        $this->historyDcImplement = $historyDcImplement;
    }

    public function getHistory()
    {
        try {
            $data = $this->historyDcImplement->getHistory(DB::connection());
        } catch (\Exception $e) {
            return $e;
        }

        return response($data, 200)->header('Content-Type', 'application/json');
    }
}
