<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\DcImplements\ObservationDcImplement;

class ObservationController extends Controller
{
    private $observationDcImplement;

    public function __construct(ObservationDcImplement $observationDcImplement)
    {
        $this->observationDcImplement = $observationDcImplement;
    }

    /**
     *Crear observación
     * 
     * @param Illuminate\Support\Facades\DB $connection
     * @param string $note 
     * @param string $email
     * @param integer $observation_type
     * @return string
     */
    public function createObservation(Request $request)
    {
        try {
            if (!$request->filled('note')) throw new \Exception("status es requerido", 400);
            // if (!$request->filled('email')) throw new \Exception("status es requerido", 400);
            if (!$request->filled('observation_type')) throw new \Exception("status es requerido", 400);

             $this->observationDcImplement->createObservation(
                DB::connection(),
                $request->note,
                $request->email,
                $request->observation_type,
            );
        } catch (\Exception $e) {
            return $e;
        }

        return response('Tu comentario ha sido enviado correctamente', 200)->header('Content-Type', 'application/json');
    }
}
