<?php

namespace  App\Libraries;


use App\Libraries\Pyrus;
use App\Libraries\Reworker;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Http;


class ReworkerWriteOffPyrus
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



    public function getItem($task_fields)
    {
        $i = 0;
        $result = [];
        foreach ($task_fields as $key => $v) {




            if ($v['id'] == 1) {

                foreach ($v['value'] as $key => $v2) {

                    $i += 1;
                    $row_content = [
                        "positionId" => $i,
                        "МастерФайлДМ" => '',
                        "Кол-во" => '',
                        "Свободный остаток" => '',
                        "Код товара" => '',


                    ];
                    foreach ($v2['cells'] as $key => $v3) {

                        foreach ($v3 as $key => $v4) {
                            if ($v3['id'] == 3) {

                                $row_content['Кол-во'] = $v3['value'];
                            }
                            if ($v3['id'] == 4) {
                                $row_content['Свободный остаток'] = $v3['value'];
                            }
                            if ($v3['id'] == 5) {
                                $row_content['Код товара'] = $v3['value'];
                            }
                            if ($v3['id'] == 2) {

                                if ($key == 'value') {
                                    foreach ($v4 as $key => $v5) {
                                        if ($key == 'values') {
                                            $row_content['МастерФайлДМ'] = $v5[3];
                                        }
                                    }
                                }
                            }
                        }
                    }
                    array_push($result, $row_content);
                }
            }
        }
        return $result;
    }
    public function getCommentRW($task_fields)
    {
        foreach ($task_fields as $key => $v) {
            if ($v['id'] == 20) {
                if (isset($v['value'])) {
                    return $v['value'];
                } else {
                    return null;
                }
            }
        }
    }


    public function getChermarkAlreadySend_id($task_fields)
    {
        foreach ($task_fields as $key => $value) {

            if ($value['id'] == 22) {
                return $value['id'];
            }
        }
    }
    public function getChermarkAlreadySend_value($task_fields)
    {
        foreach ($task_fields as $key => $value) {

            if ($value['id'] == 22) {
                return $value['value'];
            }
        }
    }
    public function getCheckmark_id($task_fields)
    {
        foreach ($task_fields as $key => $value) {

            if ($value['id'] == 21) {
                return $data = [
                    'id' => $value['id'],
                    'value' => $value['value'],
                ];
            }
        }
    }
    public function getStatusrw_id($task_fields)
    {
        foreach ($task_fields as $key => $value) {

            if ($value['id'] == 23) {
                return $value['id'];
            }
        }
    }
    public function getStatusrw_value($task_fields)
    {
        foreach ($task_fields as $key => $value) {

            if ($value['id'] == 23) {
                if (isset($value['value'])) {
                    return $value['value'];
                }
            }
        }
    }
    public function getCoderw_value($task_fields)
    {
        foreach ($task_fields as $key => $value) {

            if ($value['id'] == 24) {
                return $value['value'];
            }
        }
    }
    public function get1Cstatus_id($task_fields)
    {
        foreach ($task_fields as $key => $value) {

            if ($value['id'] == 25) {
                return $value['id'];
            }
        }
    }
    public function get1Cnumber_id($task_fields)
    {
        foreach ($task_fields as $key => $value) {

            if ($value['id'] == 26) {
                return $value['id'];
            }
        }
    }
    public function  get1Cnumber_value($task_fields)
    {
        foreach ($task_fields as $key => $value) {

            if ($value['id'] == 26) {
                if (isset($value['value'])) {
                    return $value['value'];
                } else {
                    return null;
                }
            }
        }
    }

    public function getCoderw_id($task_fields)
    {
        foreach ($task_fields as $key => $value) {
            if ($value['id'] == 24) {
                return $value['id'];
            }
        }
    }
    public function getAuther($task_fields)
    {
        foreach ($task_fields as $key => $value) {
            if ($value['id'] == 17) {
                return $value['value']['last_name'] . " " . $value['value']['first_name'];
            }
        }
    }

    public function setStatusCommand($arr)
    {
        $tt = self::REWORKER_STATUSES;
        $status = $this->getCoderw_value($arr['task']['fields']);

        $statusrw = (new Reworker())->getStatusByExtID($arr['task']['id']);
        foreach ($tt as $key => $codeStatus) {
            if ($codeStatus == $status) {
                $pyrus_status = $key;
            }
        }

        if ($pyrus_status != $statusrw) {

            $data = [
                'formatted_text' => 'Cтатус заказа ' . $statusrw,
                'field_updates' => [
                    [
                        "id" => $this->getCoderw_id($arr['task']['fields']),
                        "value" => $tt[$statusrw]
                    ],
                    [
                        "id" => $this->getStatusrw_id($arr['task']['fields']),
                        "value" => $statusrw
                    ],
                ],
            ];

            (new Pyrus())->addComment($arr['task']['id'], $data);
        }
    }
    public function makeQuery($task_id, $test)
    {
        $task_fields = (new Pyrus())->getTaskFields($task_id);
        $comment = $this->getCommentRW($task_fields);
        $current_date = date("Y-m-d");
        $res = [];
        foreach ($test as $key => $value) {
            $getrw = (new Reworker())->getRw($value['Код товара']);
            $orderProducts = [
                'productOffer' => 's',
                'count' => '',
                'price' =>  '',
                'total' => ''
            ];
            $orderProducts['productOffer'] =  $getrw['id'];
            $orderProducts['count'] = $value['Кол-во'];
            $orderProducts['price'] = 1;
            $orderProducts['total'] = 1 * $value['Кол-во'];
            array_push($res, $orderProducts);
        }

        $json = [

            'extId' => $task_id,
            'date' => $current_date,
            'shipmentDate' => $current_date,
            'shop' => 186068,

            'paymentState' => 'not_paid',
            'orderPrice' => 1,
            'totalPrice' => 1,
            'deliveryRequest' => [
                'sender' => 9310,
                'deliveryDate' => $current_date
            ],
            'eav' => [
                'document' => $current_date
            ],
            'orderProducts' => $res,
            'comment' => $comment,
            'phone' => '',
            'profile' => [
                'name' => $this->getAuther($task_fields),
                'type' => 'physical'
            ]

        ];

        return  $json;
    }
}
