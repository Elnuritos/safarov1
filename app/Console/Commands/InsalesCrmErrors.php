<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class InsalesCrmErrors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:crm_errors';

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

        $json = Redis::RPOP('crm_insales_errors');
        $task = json_decode($json, JSON_UNESCAPED_UNICODE);



            try {

                $response = Http::post(getenv('CRM_API_URL'), [
                    'action' => 'Site/createOrderTaskLink',
                    'task_id' => $task['task_id'],
                    'order_id' => $task['order_id'],
                ])->json();
                $values=explode(", ",$response['response']);
                if (empty($values[0])==true || empty($values[1])==true ){
                    throw new \Exception("Срм выдал ошибку");
                }
            } catch (\Exception$e) {


                Redis::LPUSH('crm_insales_errors', json_encode($task));
            }

    }
}
