<?php

namespace App\Console\Commands;

use App\Libraries\Pyrus;
use App\Libraries\PyrusSberForm;
use App\Models\Sber;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Insales\Library\Insales as ApiInsales;

class InsalesSber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:insales_sber';

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
            $text = [
                "0" => "заказ зарегистрирован, но не оплачен",
                "1" => "предавторизованная сумма удержана (для двухстадийных платежей)",
                "2" => "заказ оплачен.значение поля Статус оплаты изменено на оплачено.",
                "3" => "авторизация отменена",
                "4" => "по транзакции была проведена операция возврата",
                "5" => "инициирована авторизация через сервер контроля доступа банка-эмитента",
                "6" => "авторизация отклонена.",
            ];

            $json = Redis::RPOP('online_payment_insales');
            $task = json_decode($json, JSON_UNESCAPED_UNICODE);
            $url = "https://323f9baffa77dfd407d9ee42731e3d7a:14aab7f0d7a89a0c0aa52dd4d54fc29c@dimaestri.com";
            $client = new ApiInsales($url);
            $order_update = $client->getOrderById($task['order_id'])->getResponse();
            $pyrus = new Pyrus();
            $task_fields = $pyrus->getTaskFields($task['task_id']);
            $id = (new PyrusSberForm())->getPaymentIdField($task_fields);
            $value = (new PyrusSberForm())->getPaymentValue($task_fields);
            if ($value != 1) {

                if ($order_update['data']['financial_status'] == "paid") {

                    $data = [
                        'formatted_text' => 'Получен новый статус платежа - ' . $text[2],
                        'approval_choice' => 'approved',
                        'field_updates' => [[
                            "id" => $id,
                            "value" => [
                                'choice_id' => 1,
                            ],
                        ]],
                    ];
                    $comment = $pyrus->addComment($task['task_id'], $data);
                    if (isset($comment)) {
                        $i = 1;
                    } else {
                        $i = 0;
                    }
                    $sber = Sber::create([
                        'payment_id' => $_GET['orderNumber'],
                        'task_id' => $task['task_id'],
                        'status' => 2,
                        'exported' => $i,
                    ]);

                } else {
                    Redis::LPUSH('online_payment_insales', json_encode($task));
                }
            }
        } catch (\Exception$e) {
            Log::channel('daily')->info("Заказ {$task['task_id']} не оплачен");
            Redis::LPUSH('online_payment_insales', json_encode($task));
        }

    }
}
