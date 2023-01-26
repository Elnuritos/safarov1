<?php

namespace App\Console\Commands;

use App\Libraries\Pyrus;
use App\Libraries\QrCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetSbpStatus extends Command
{


    const ACTIVE_STATUSES =   [
        "CREATED", "PAID",  "REVERSED", "REFUNDED", "REVOKED", "DECLINED", "EXPIRED", "AUTHORIZED", "CONFIRMED", "ON_PAYMENT"
    ];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sbp_status:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get sbp status';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $task_list = (new Pyrus())->getFormTasks(1046999, [
            'fld216' =>  "CREATED", "PAID",  "REVERSED", "REFUNDED", "REVOKED", "DECLINED", "EXPIRED", "AUTHORIZED", "CONFIRMED", "ON_PAYMENT"
        ]);

        if (array_key_exists('tasks', $task_list)) {

            $task_list = $task_list['tasks'];
            $kitForm = [];
            foreach ($task_list as $task) {
                $fields = $task['fields'];
                foreach ($fields as $item) {
                    if ($item['id'] == 216) {
                        if ($item['value'] != 'PAID' && $item['value'] != 'DECLINED') {
                            $kitForm['task'] = $task;
                            $t =  (new QrCode())->setStatusCommand($kitForm);
                        }
                    }
                }
            }
        }

        return 0;
    }
}
