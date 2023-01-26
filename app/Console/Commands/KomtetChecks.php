<?php

namespace App\Console\Commands;

use App\Libraries\Pyrus;
use Illuminate\Console\Command;
use App\Libraries\KomtetCheckPyrus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class KomtetChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:check';

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
        $task_list = (new Pyrus())->getFormTasks(1046999, [
            'fld216' => 'PAID'
        ]);

        //Redis::LPUSH('validated_tasks_for_insales', json_encode($task_list));
        // $json = Redis::RPOP('validated_tasks_for_insales');
        //$task1 = json_decode($json, JSON_UNESCAPED_UNICODE);
        try {
            if (array_key_exists('tasks', $task_list)) {

                $task_list = $task_list['tasks'];
                $kitForm = [];
                foreach ($task_list as $task) {
                    $task_id = $task['id'];
                    $data = (new KomtetCheckPyrus())->getVal($task_id);
                }
            }
            Redis::LPUSH('komtet_checks', json_encode($data));

        } catch (\Exception $e) {
            Log::channel('daily')->warning("Не удалось провести постобработку заказ , возвращаю его в очередь!", ['msg' => $e]);
          //  Redis::LPUSH('validated_tasks_for_insales', $task);

            return 0;
        }
        return 0;
    }
}
