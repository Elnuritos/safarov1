<?php

namespace App\Console\Commands;

use App\Models\InsalesProducts;
use Illuminate\Console\Command;
use App\Jobs\InsalesUpdateBundle;
use Illuminate\Support\Facades\Log;
use App\Libraries\GetInsalesProduct;
use Illuminate\Support\Facades\Redis;
use App\Libraries\UpdateInsalesReworker;

class GetInSalesProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:insales';

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

       //(new GetInsalesProduct)->getProducts();

     /*    $task1 =
            [
            'pyrus_id' => 1,
            'reworker_id' => 26317567,
            'insales_id' => 65613663,
        ]; */
     //   $json = json_encode($task1);
        $json = Redis::RPOP('validated_tasks_for_insales');
        $task = json_decode($json, JSON_UNESCAPED_UNICODE);

        try {
            $action=new UpdateInsalesReworker($task['insales_id'],$task['reworker_id']);
            InsalesUpdateBundle::dispatch($action);

        } catch (\Exception$e) {
            Log::channel('daily')->warning("Не удалось провести постобработку заказ {$task['pyrus_id']}, возвращаю его в очередь!", ['msg' => $e]);
            Redis::LPUSH('validated_tasks_for_insales', json_encode($task));
        }

        return 0;
    }
}
