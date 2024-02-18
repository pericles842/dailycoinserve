<?php

namespace App\Console\Commands;

use App\Http\DcImplements\HistoryDcImplement;
use App\Http\DcImplements\BankingEntityDcImplement;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class HistorySetBank extends Command
{
    private $historyDcImplement;
    private $bankingEntityDcImplement;

    /**
     * Nombre de mi comando
     *
     * @var string
     */
    protected $signature = 'set:history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Guarda un registro del precio según el dia';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        HistoryDcImplement $historyDcImplement,
        BankingEntityDcImplement $bankingEntityDcImplement
    ) {
        $this->historyDcImplement = $historyDcImplement;
        $this->bankingEntityDcImplement = $bankingEntityDcImplement;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            //*si se acaba la semana eliminara los registros
            $list_history = $this->historyDcImplement->getDayWeekRate(DB::connection(), 6);

            if (!empty($list_history)) $this->historyDcImplement->deleteHistoryRecord(DB::connection());

            //extraigo variables
            $bcv = $this->bankingEntityDcImplement->getBcv();
            $array_entities = $this->bankingEntityDcImplement->getRateList();
            $en_paralelo = null;

            //extraigo enparalelo 
            foreach ($array_entities as $entity) {
                if ($entity['key'] == 'enparalelovzla') {
                    $en_paralelo = $entity;
                    break;
                }
            }

            //carga diaria de x cantidad de bancos
            $carga_diaria = array_merge([$bcv["currency"][1]], [$en_paralelo]);


            //verificamos la carga
            foreach ($carga_diaria as $carga) {
                $this->historyDcImplement->createNewHistory(DB::connection(), $carga["name"], str_replace(',', '.',  $carga["price"]), date('w'), 'neutro');
            }
        } catch (\Exception $e) {
            // Manejo de excepciones
            // Puedes mostrar un mensaje de error, registrar el error en un archivo de registro, etc.
            // Por ejemplo:
            echo 'Error: ' . $e->getMessage();
        }
    }
}
