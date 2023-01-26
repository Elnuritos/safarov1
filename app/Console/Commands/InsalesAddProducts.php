<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Libraries\GetInsalesProduct;

class InsalesAddProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:insales_products';

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
            (new GetInsalesProduct)->getProducts();
        } catch (\Exception $e) {
            Log::channel('daily')->warning("Не удалось провести обработку добавления товаров", ['msg' => $e]);
        }
    }
}
