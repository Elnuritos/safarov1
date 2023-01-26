<?php

namespace App\Console\Commands;

use App\Libraries\Pyrus;
use Illuminate\Console\Command;
use App\Libraries\ReworkerPyrus;
use Illuminate\Support\Facades\Log;
use App\Libraries\ReworkerWriteOffPyrus;

class GetStatusRW extends Command
{
    const REWORKER_STATUSES = [
        'pending_queued' => 1,
        'cancel' => 2,
        'new' => 3,
        'pending' => 4,
        'pending_error' => 5,
        'error' => 6,
        'partly_reserved' => 7,
        'download' => 8,
        'deleted' => 9,
        'reserve_queued' => 10,
        'reserved' => 11,
        'confirmed' => 12,
        'assembling' => 13,
        'assembled' => 14,
        'processing' => 15,
        'delivery' => 16,
        'return' => 17,
        'partly_return' => 18,
        'complete' => 19
    ];

    const ACTIVE_STATUSES = [
        1, 3, 4, 7, 10, 11, 12, 13, 14, 15, 16
    ];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "rw_status:get";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get rw status';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        foreach (self::ACTIVE_STATUSES as $codeStatus) {
            $task_list = (new Pyrus())->getFormTasks(1138320, [
                'fld24' => $codeStatus
            ]);
            if (array_key_exists('tasks', $task_list)) {
                $task_list = $task_list['tasks'];
                $kitForm = [];
                foreach ($task_list as $task) {
                    $fields = $task['fields'];
                    foreach ($fields as $item) {
                        if ($item['id'] == 24) {
                            if ($item['value'] != 'complete' && $item['value'] != 'cancel') {
                                $kitForm['task'] = $task;
                                (new ReworkerWriteOffPyrus())->setStatusCommand($kitForm);
                                // dd($t);
                            }
                        }
                    }
                }
            }
        }
        return 0;
    }
}
