<?php

namespace App\Console\Commands;

use App\Http\DcImplements\HistoryDcImplement;
use App\Http\DcImplements\BankingEntityDcImplement;
use Dotenv\Parser\Value;
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
            //extraigo LOS OBJETOS de las tasas
            $bcv = $this->bankingEntityDcImplement->getBcv();
            $array_entities = BankingEntityDcImplement::getRateList();

            //*Buscamos emparalelo
            $en_paralelo = HistoryDcImplement::searchBankForName($array_entities, 'enparalelovzla', 'key');


            //obtengo es estadus de bcv, IMPORTANTE TENER UN REGISTRO DEL DIA ANTERIOR
            $status_bcv = HistoryDcImplement::getStatusBcvToday(DB::connection(),  date('w'), $bcv["currency"][1]['price']);
            
            $bcv_object = [
                'name' => "BCV",
                'price' => $bcv["currency"][1]['price'],
                'label_status' => $status_bcv

            ];

            $list_history = HistoryDcImplement::getDayWeekRate(DB::connection(), 6);

            //*si se acaba la semana eliminara los registros

            if (!empty($list_history)) HistoryDcImplement::deleteHistoryRecord(DB::connection());

            //carga diaria de x cantidad de bancos
            $carga_diaria = array_merge([$bcv_object], [$en_paralelo]);


            foreach ($carga_diaria as $carga) {
                $this->historyDcImplement->createNewHistory(
                    DB::connection(),
                    $carga["name"],
                    str_replace(',', '.',  $carga["price"]),
                    date('w'),
                    $carga["label_status"]
                );
            }
        } catch (\Exception $e) {
            // Manejo de excepciones
            // Puedes mostrar un mensaje de error, registrar el error en un archivo de registro, etc.
            // Por ejemplo:
            echo 'Error: ' . $e->getMessage();
        }
    }
}
