<?php

namespace  App\Libraries;


use App\Models\Sber;
use App\Models\Insales;
use App\Libraries\Pyrus;
use GuzzleHttp\HandlerStack;
use App\Libraries\PyrusSberForm;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Http;
use Voronkovich\SberbankAcquiring\Client;
use Voronkovich\SberbankAcquiring\Currency;
use Insales\Library\Insales as ApiInsales;

class InsalesSberCallback
{
    public function getCallback($request)
    {
     //  dd($request);
        $orderId=$_GET['mdOrder'];
       // $orderId=$request->key;
        $text = [
            "0" => "заказ зарегистрирован, но не оплачен",
            "1" => "предавторизованная сумма удержана (для двухстадийных платежей)",
            "2" => "заказ оплачен.значение поля Статус оплаты изменено на оплачено.",
            "3" => "авторизация отменена",
            "4" => "по транзакции была проведена операция возврата",
            "5" => "инициирована авторизация через сервер контроля доступа банка-эмитента",
            "6" => "авторизация отклонена.",
        ];

        $client = new Client(['userName' => getenv('SBER_API_USERNMAE'), 'password' =>  getenv('SBER_API_PASSWORD')]);
        $status = $client->execute('/payment/rest/getOrderStatusExtended.do', [
            'orderId' => $orderId,
        ]);

        foreach ($status['merchantOrderParams'] as $key => $value) {
            if ($value['name']=='id') {
                $insales_id=$value['value'];
            }
        }
        $task_id= Insales::where('order_id',$insales_id)->value('task_id');

        $pyrus = new Pyrus();
        $task_fields = $pyrus->getTaskFields($task_id);
        $id = (new PyrusSberForm())->getPaymentIdField($task_fields);
        if ($status['orderStatus'] == 2) {
            $url = "https://323f9baffa77dfd407d9ee42731e3d7a:14aab7f0d7a89a0c0aa52dd4d54fc29c@dimaestri.com";
            $client = new ApiInsales($url);
            $insales_data['order'] = [
                "financial_status" => "paid",
            ];
            //https://safarov.dimaestri-serve.ru/api/insalescallback
            $order_update= $client->updateOrder($insales_id, $insales_data)->getResponse();
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
