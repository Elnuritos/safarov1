<?php

namespace App\Libraries;

use App\Libraries\Pyrus;
use App\Libraries\PyrusSberForm;
use Illuminate\Support\Facades\Log;
use Voronkovich\SberbankAcquiring\Client;
use Voronkovich\SberbankAcquiring\Currency;

class SberForm
{
    public function getForm($task_id)
    {
        $pyrus = new Pyrus();
        $pyrusSber = new PyrusSberForm();
        $task_fields = $pyrus->getTaskFields($task_id);
        $ship = $pyrusSber->getShipment($task_fields);
        $bonus = $pyrusSber->getBonus($task_fields);
        $phone = $pyrusSber->getPhone($task_fields);
        $mail = $pyrusSber->getMail($task_fields);
        $tovar = $pyrusSber->getTable($task_fields);
        $money = $pyrusSber->getFieldValue($task_fields);
        $upd = $pyrusSber->getUpd($task_fields);
        if ($upd != 0) {
            $money += $upd;
        }
        $items = $pyrusSber->getItems($tovar, $ship, $bonus, $money, $upd);
        $checker = $pyrusSber->getPaymentValue($task_fields);
        if ($checker == 1) {

            $view = [
                "status" => "Оплата прошла",
            ];
            return $view;
        } else {
            $orderBundle = [
                "customerDetails" => [
                    "phone" => $phone,
                    "email" => $mail,
                ],
                "cartItems" => [
                    "items" => $items,
                ],
            ];
            $view = [
                "status" => "Создание",
                'bonus' => $bonus,
                'ship' => $ship,
                'task_id' => $task_id,
                'amount' => $money,
                "orderBundle" => [
                    "customerDetails" => [
                        "phone" => $phone,
                        "inn" => "",
                        "email" => $mail,
                    ],
                    "cartItems" => [
                        "items" => $items,
                    ],
                ],
            ];
            Log::channel('daily')->info("Закакз {$task_id}", $view);
            return $view;
        }
    }
    public function Pay($task_id)
    {
        $pyrus = new Pyrus();
        $pyrusSber = new PyrusSberForm();
        $task_fields = $pyrus->getTaskFields($task_id);
        $ship = $pyrusSber->getShipment($task_fields);
        $bonus = $pyrusSber->getBonus($task_fields);
        $phone = $pyrusSber->getPhone($task_fields);
        $mail = $pyrusSber->getMail($task_fields);
        $tovar = $pyrusSber->getTable($task_fields);
        $money = $pyrusSber->getFieldValue($task_fields);
        $upd = $pyrusSber->getUpd($task_fields);
        if ($upd != 0) {
            $money += $upd;
        }
        $items = $pyrusSber->getItems($tovar, $ship, $bonus, $money, $upd);
        $checker = $pyrusSber->getPaymentValue($task_fields);

        if ($checker == 1) {
            $view = "Оплата прошла";
        } else {

            $orderBundle = [
                "customerDetails" => [
                    "phone" => $phone,
                    "email" => $mail,
                ],
                "cartItems" => [
                    "items" => $items,
                ],
            ];

            //sber
            $client = new Client(['userName' => getenv('SBER_API_USERNMAE'), 'password' => getenv('SBER_API_PASSWORD')]);
            $orderId = $task_id . '-x-' . rand();

            $orderAmount = $money * 100;
            $returnUrl = getenv('PAYMENT_SUCCESS_URL') . $task_id;

            $params['currency'] = Currency::RUB;
            $params['failUrl'] = getenv('PAYMENT_FAIL_URL') . $task_id;
            $params['orderBundle'] = $orderBundle;

            $params['dynamicCallbackUrl'] = 'https://safarov.dimaestri-serve.ru/api/callback?orderNumber=' . $orderId;
            $result = $client->registerOrder($orderId, $orderAmount, $returnUrl, $params);

            $orderId = $result['orderId'];
            $paymentFormUrl = $result['formUrl'];
            return $paymentFormUrl;
        }
    }
}
