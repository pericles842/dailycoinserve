<?php

namespace App\Console\Commands;

use App\Http\DcImplements\HistoryDcImplement;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class HistorySetBank extends Command
{
    private $historyDcImplement;

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
    public function __construct(HistoryDcImplement $historyDcImplement)
    {
        $this->historyDcImplement = $historyDcImplement;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->historyDcImplement->createNewHistory(DB::connection(),'Banco prueba 3',2.90,1,'neutro');
    }
}
