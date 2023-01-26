<?php

namespace  App\Libraries;


use App\Libraries\Pyrus;
use App\Libraries\QrAuth;
use App\Libraries\QrCode;
use App\Libraries\QrCreate;
use GuzzleHttp\HandlerStack;
use App\Libraries\PyrusSberForm;
use Illuminate\Support\Facades\Http;
use Voronkovich\SberbankAcquiring\Client;
use Voronkovich\SberbankAcquiring\Currency;


class SberComment
{

    public function getComment($task_id)
    {
        $pyrus = new Pyrus();
        $pyrusSber = new PyrusSberForm();
        $task_fields = $pyrus->getTaskFields($task_id);
        $url_value = $pyrusSber->getUrlFieldValue($task_fields);
        $url_id = $pyrusSber->getUrlFieldId($task_fields);
        $payment_method = $pyrusSber->getPaymentMethod($task_fields);
        $bonus = $pyrusSber->getBonus($task_fields);
        $ship = $pyrusSber->getShipment($task_fields);
        $phone = $pyrusSber->getPhone($task_fields);
        $mail = $pyrusSber->getMail($task_fields);
        $tovar = $pyrusSber->getTable($task_fields);
        $money = $pyrusSber->getFieldValue($task_fields);
        $upd = $pyrusSber->getUpd($task_fields);
        $items = $pyrusSber->getItems($tovar, $ship, $bonus, $money, $upd);

        if (is_null($url_value) && $payment_method == '3') {
            if (!isset($phone)) {

                $data = [
                    'formatted_text' => "Для формирования ссылки онлайн платежа не хватает номера телефона"
                ];

                return $pyrus->addComment($task_id, $data);
            }
            if (!isset($mail)) {

                $data = [
                    'formatted_text' => "Для формирования ссылки онлайн платежа не хватает - Email"
                ];

                return $pyrus->addComment($task_id, $data);
            }
            $p_url = getenv('PAYMENT_URL') . $task_id;
            $scope = getenv('QR_SBP_CREATE');
            $token = (new QrAuth())->auth($scope);
            $res = (new QrCreate())->create($task_id, $token);

            $data = [
                'formatted_text' => "Ссылка для оплаты - " . $p_url . "<br>(Тест) Ссылка для оплаты через Qr-code - " . $res['order_form_url'],
                'field_updates' => [
                    [
                        "id" => $url_id,
                        "value" => $p_url
                    ], [
                        "id" => (new QrCode())->getQrStatusId($task_fields),
                        "value" => $res['order_state']
                    ],
                    [
                        "id" => (new QrCode())->getQrID($task_fields),
                        "value" => $res['order_id']
                    ]
                ]
            ];
            return $pyrus->addComment($task_id, $data);
        } /* else {
    $p_url = getenv('PAYMENT_URL') . $task_id;
    $scope = getenv('QR_SBP_CREATE');
    $token = (new QrAuth())->auth($scope);
    $res = (new QrCreate())->create($task_id, $token);
    $data = [
        'formatted_text' => "Ссылка для оплаты - " . $p_url . "<br> Ссылка для оплаты через Qr-code - " . $res['order_form_url'],

    ];
}
 */
        return 0;
    }
}
