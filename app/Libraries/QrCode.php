<?php

namespace  App\Libraries;


use App\Libraries\QrAuth;
use GuzzleHttp\HandlerStack;
use App\Libraries\PyrusSberForm;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class QrCode
{
    const ACTIVE_STATUSES =   [
        "CREATED", "PAID",  "REVERSED", "REFUNDED", "REVOKED", "DECLINED", "EXPIRED", "AUTHORIZED", "CONFIRMED", "ON_PAYMENT"
    ];
    public function getQrID($task_fields)
    {
        foreach ($task_fields as $key => $v) {

            if ($v['id'] == 217) {

                return $v['id'];
            }
        }
    }
    public function getQrIdValue($task_fields)
    {
        foreach ($task_fields as $key => $v) {

            if ($v['id'] == 217) {
                if (!isset($v['value'])) {
                    return NULL;
                } else {
                    return $v['value'];
                }
            }
        }
    }

    public function getQrStatusId($task_fields)
    {
        foreach ($task_fields as $key => $v) {

            if ($v['id'] == 216) {

                return $v['id'];
            }
        }
    }
    public function getQrStatusValue($task_fields)
    {
        foreach ($task_fields as $key => $v) {

            if ($v['id'] == 216) {
                if (!isset($v['value'])) {
                    return NULL;
                } else {
                    return $v['value'];
                }
            }
        }
    }
    public function setStatusCommand($arr)
    {

        $tt = self::ACTIVE_STATUSES;
        $status = $this->getQrStatusValue($arr['task']['fields']);
        $order_id = $this->getQrIdValue($arr['task']['fields']);
        $id = (new PyrusSberForm())->getPaymentIdField($arr['task']['fields']);

        $task_id = $arr['task']['id'];

        $scope_status = getenv('QR_SBP_STATUS');
        $status_token = (new QrAuth())->auth($scope_status);
        $res_status = (new QrStatus())->status($task_id, $status_token, $order_id);


        $statussbp = $res_status['order_state'];
        foreach ($tt as $key => $codeStatus) {
            if ($codeStatus == $status) {
                $pyrus_status = $codeStatus;
            }
        }

        if ($pyrus_status != $statussbp) {
            if ($statussbp == 'PAID') {

                $data = [
                    'formatted_text' => 'Новый статус оплаты ' . $statussbp,
                    'field_updates' => [
                        [
                            "id" => $this->getQrStatusId($arr['task']['fields']),
                            "value" => $statussbp
                        ],
                        [
                            "id" => $id,
                            "value" => [
                                'choice_id' => 1
                            ]
                        ],
                    ],
                ];
                try {

                    $json = (new KomtetCheckPyrus())->getVal($task_id);
                    Redis::LPUSH('komtet_checks', json_encode($json));

                } catch (\Exception $e) {
                    Log::channel('daily')->warning("Не удалось провести постобработку заказ , возвращаю его в очередь!", ['msg' => $e]);
                }
            } else {
                $data = [
                    'formatted_text' => 'Новый статус оплаты ' . $statussbp,
                    'field_updates' => [

                        [
                            "id" => $this->getQrStatusId($arr['task']['fields']),
                            "value" => $statussbp
                        ],
                    ],
                ];
            }

            (new Pyrus())->addComment($arr['task']['id'], $data);
        }

        return 0;
    }
}
