<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\DcImplements\BankingEntityDcImplement;

class BankingEntityController extends Controller
{
    private $BankingEntityDcImplement;

    public function __construct(BankingEntityDcImplement $BankingEntityDcImplement)
    {
        $this->BankingEntityDcImplement = $BankingEntityDcImplement;
    }

    public function getBcv()
    {
        try {
            $data = $this->BankingEntityDcImplement->getBcv();
        } catch (\Exception $e) {
            return $e;
        }

        return response($data, 200)->header('Content-Type', 'application/json');
    }


    public function getEnParalelo()
    {
        try {
            $data = $this->BankingEntityDcImplement->getEnParalelo();
        } catch (\Exception $e) {
            return $e;
        }

        return response($data, 200)->header('Content-Type', 'application/json');
    }
}
