<?php

namespace App\Http\DcImplements;


class ObservationDcImplement
{
    /**
     * Crear observación
     * 
     * @param Illuminate\Support\Facades\DB $connection
     * @param string $note 
     * @param string $email
     * @param integer $observation_type
     * @return null
     */
    public function createObservation(
        $connection,
        $note,
        $email,
        $observation_type
    ) {
        $data = [
            "note" => $note,
            "email" => $email,
            "observation_type" => $observation_type
        ];

        return $connection->table('observation')->insert($data);
    }
}
