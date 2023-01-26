<?php

namespace  App\Libraries;


use App\Models\Sber;
use App\Libraries\Pyrus;
use GuzzleHttp\HandlerStack;
use App\Libraries\PyrusSberForm;
use Illuminate\Support\Facades\Http;
use Voronkovich\SberbankAcquiring\Client;
use Voronkovich\SberbankAcquiring\Currency;


class SberCallback
{
    public function getCallback()
    {
        $task_arr = explode("-", $_GET['orderNumber']);
        $task_id = $task_arr[0];
        $pyrus = new Pyrus();
        $text = [
            "0" => "заказ зарегистрирован, но не оплачен",
            "1" => "предавторизованная сумма удержана (для двухстадийных платежей)",
            "2" => "заказ оплачен.значение поля Статус оплаты изменено на оплачено.",
            "3" => "авторизация отменена",
            "4" => "по транзакции была проведена операция возврата",
            "5" => "инициирована авторизация через сервер контроля доступа банка-эмитента",
            "6" => "авторизация отклонена.",
        ];
        $task_fields = $pyrus->getTaskFields($task_id);
        $id = (new PyrusSberForm())->getPaymentIdField($task_fields);
        $client = new Client(['userName' => getenv('SBER_API_USERNMAE'), 'password' =>  getenv('SBER_API_PASSWORD')]);
        $status = $client->execute('/payment/rest/getOrderStatusExtended.do', [
            'orderNumber' => $_GET['orderNumber'],
        ]);

        if ($status['orderStatus'] == 2) {
            $data = [
                'formatted_text' => 'Получен новый статус платежа - ' . $text[$status['orderStatus']],
                'approval_choice' => 'approved',
                'field_updates' => [[
                    "id" => $id,
                    "value" => [
                        'choice_id' => 1
                    ]
                ]],
            ];
            $comment =  $pyrus->addComment($task_id, $data);
            if (isset($comment)) {
                $i = 1;
            } else {
                $i = 0;
            }
            $sber =   Sber::create([
                'payment_id' => $_GET['orderNumber'],
                'task_id' => $task_id,
                'status' => $status['orderStatus'],
                'exported' => $i,
            ]);


            return  $comment;
        } else {
            if (Sber::where('task_id', $task_id)->first() == null) {
                $data = [
                    'formatted_text' => 'Получен новый статус платежа - ' . $text[$status['orderStatus']],
                    'field_updates' => [[
                        "id" => $id,
                        "value" => [
                            'choice_id' => 2
                        ]
                    ]],
                ];

                $comment =  $pyrus->addComment($task_id, $data);

                if (isset($comment)) {
                    $i = 1;
                } else {
                    $i = 0;
                }

                $sber =   Sber::create([
                    'payment_id' => $_GET['orderNumber'],
                    'task_id' => $task_id,
                    'status' => $status['orderStatus'],
                    'exported' => $i,
                ]);

                return  $comment;
            } else {
                $sber =   Sber::create([
                    'payment_id' => $_GET['orderNumber'],
                    'task_id' => $task_id,
                    'status' => $status['orderStatus'],

                ]);
            }
        }
    }
}
