<?php

namespace App\Console\Commands;

use App\Libraries\Scv;
use App\Libraries\Pyrus;
use App\Models\InsalesProducts;
use Illuminate\Console\Command;
use App\Libraries\InsalesStatus;
use App\Models\ReworkerStatuses;
use App\Models\InsalesUpdateOrders;
use Illuminate\Support\Facades\Log;

class GetInsalesStatus extends Command
{
    const ACTIVE_STATUSES = [
        1, 3, 4, 7, 10, 11, 12, 13, 14, 15, 16
    ];
    const REWORKER_STATUSES = [
        1 =>  'pending_queued',
        12 =>  'confirmed',
        13 =>  'assembling',
        16 =>  'delivery',
        19 =>  'complete'
    ];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insales_status:get';

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

       // $l = (new Scv)->load();

        foreach (self::REWORKER_STATUSES as $key => $codeStatus) {
            $task_list = (new Pyrus())->getFormTasks(1046999, [
                //  "created_after" => date("Y-m-d", strtotime('-1 week')) . 'T10:00:00Z',
                'fld49' => 1,
                'fld201' => $key
            ]);
            if (array_key_exists('tasks', $task_list)) {
                $task_list = $task_list['tasks'];
                $kitForm = [];
                foreach ($task_list as $task) {
                    $fields = $task['fields'];
                    foreach ($fields as $item) {
                        if ($item['id'] == 201) {
                            if ($item['value'] != 'complete' && $item['value'] != 'cancel') {
                                $kitForm['task'] = $task;
                                (new InsalesStatus)->getStatus($kitForm, $codeStatus);
                            }
                        }
                    }
                }
            }
        }
        return 0;
    }
    /*  $memory = memory_get_usage();
        $te = $this->getGoods1();
        dd($te,  memory_get_usage() - $memory . ' байт'); */
    /*   $memory = memory_get_usage();
        $price = [];
        foreach ($this->getGoods() as $key => $value) {
            array_push($price, $value);
        }
        dd($price,  memory_get_usage() - $memory . ' байт');
        $test = InsalesProducts::where('price', "499")->get(); */
    /*  foreach ($test as $product) {
            yield $product;
        } */
    /*    ReworkerStatuses::create([
            "task_id" => "143385105",
            "order_status" => "complete"
        ]); */
    /*   ReworkerStatuses::truncate();
        dd(ReworkerStatuses::all()); */
    /*     private function getGoods()
    {
        $test = InsalesProducts::where('price', "499")->get();
        foreach ($test as $key => $value) {
            yield $value->price;
        }
    }
    private function getGoods1()
    {
        $test = InsalesProducts::where('price', "499")->get();
        $price = [];
        foreach ($test as $key => $value) {
            array_push($price,  $value->price);
        }
        return $price;
    } */

    /*      try {
                $task = ReworkerStatuses::where('order_status', $codeStatus)->get();
                if ($task != null) {
                    Log::channel('daily')->info("удалось провести запрос в бд");
                }
            } catch (\Exception $e) {
                Log::channel('daily')->warning("Не удалось провести запрос в бд", ['msg' => $e]);
            } */

    // $task = InsalesUpdateOrders::where('article', '0909-01')->get();

    /*     foreach ($task as $key => $value) {
                $task_id = $value->task_id;
                Log::channel('daily')->info("Номер задачи step1: {$task_id}");
                (new InsalesStatus)->getStatus($task_id, $codeStatus);
                //  dd($value->title);
            }
        } */
}
