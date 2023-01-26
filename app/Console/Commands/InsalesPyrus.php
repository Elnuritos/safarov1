<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Libraries\InsalesPyrus as PyrusIN;

class InsalesPyrus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:insales_pyrus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            (new PyrusIN)->getInsales();
        } catch (\Exception $e) {
            Log::channel('daily')->warning("Не удалось провести обработку", ['msg' => $e]);
        }
    }
}
