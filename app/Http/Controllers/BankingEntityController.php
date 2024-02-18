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

    /**
     * Obtiene del implemento  BankingEntityDcImplement las tasas del banco central
     *  
     * @return object
     */
    public function getBcv()
    {
        try {
            $data = $this->BankingEntityDcImplement->getBcv();
        } catch (\Exception $e) {
            return $e;
        }

        return response($data, 200)->header('Content-Type', 'application/json');
    }


    /**
     * Obtiene del implemento  BankingEntityDcImplement un arreglo de varias entidades
     *  
     * @return object
     */
    public function getRateList()
    {
        try {
            $data = $this->BankingEntityDcImplement->getRateList();
        } catch (\Exception $e) {
            return $e;
        }

        return response($data, 200)->header('Content-Type', 'application/json');
    }
}
