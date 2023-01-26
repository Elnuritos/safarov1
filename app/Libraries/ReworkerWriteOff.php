<?php

namespace  App\Libraries;


use App\Models\Sber;
use App\Libraries\OneC;
use App\Libraries\Pyrus;
use App\Libraries\Reworker;
use GuzzleHttp\HandlerStack;
use App\Libraries\OneCWriteOff;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Libraries\ReworkerWriteOffPyrus;
use Voronkovich\SberbankAcquiring\Client;
use Voronkovich\SberbankAcquiring\Currency;


class ReworkerWriteOff
{
    public function WriteOff($task_id)
    {
        $pyrus = new Pyrus();
        $rp = new ReworkerWriteOffPyrus();
        $task_fields = $pyrus->getTaskFields($task_id);
        $comment = $rp->getCommentRW($task_fields);
        if (!isset($comment)) {
            $data = [
                'formatted_text' => 'Добавьте комментарий в поле Комментарий для RW'
            ];
            return   $pyrus->addComment($task_id, $data);
        }
        $C_status = $rp->get1Cstatus_id($task_fields);
        $C_number = $rp->get1Cnumber_id($task_fields);
        $test = $rp->getItem($task_fields);
        $json =  $rp->makeQuery($task_id, $test);
        $check_id = $rp->getCheckmark_id($task_fields);
        $checkmark_id = $rp->getChermarkAlreadySend_id($task_fields);
        $statusrw_id = $rp->getStatusrw_id($task_fields);
        $coderw_id = $rp->getCoderw_id($task_fields);
        if ($check_id['value'] == 'unchecked') {
            $data = [
                'formatted_text' => ' поставьте галочку Отправить RW',
            ];

            return $pyrus->addComment($task_id, $data);
        }

        //  dd($rp->getChermarkAlreadySend_value($task_fields), "ss");
        if ($rp->getChermarkAlreadySend_value($task_fields) == "unchecked") {


            if ($rp->getStatusrw_value($task_fields) == null) {
                $try = (new Reworker())->createOrder($json);

                if (isset($try['id'])) {
                    Log::channel('daily')->info('Заказ №' . $task_id . ' собран для списания');
                    $data = [
                        'formatted_text' => 'Отправлено на Reworker : ' . json_encode($json, JSON_UNESCAPED_UNICODE),
                    ];
                    $pyrus->addComment($task_id, $data);
                    // dd($json);
                    $data = [
                        'formatted_text' => 'Данные отправлены на RW, формирование заказа и ответ от RW может занять несколько минут. Если прошло более 5 минут, обратитесь к разработчикам.'
                    ];
                    $pyrus->addComment($task_id, $data);
                    $data = ['formatted_text' => 'Ответ от Reworker : ' . json_encode($try, JSON_UNESCAPED_UNICODE)];
                    $pyrus->addComment($task_id, $data);
                    $data = [
                        'formatted_text' =>
                        '<br><br> Заказ на списание товара: ' . 'https://lk.reworker.ru/#/orders/orders/entity/retail/' . $try['id'],
                        'field_updates' => [
                            [
                                "id" =>  $statusrw_id,
                                "value" => 'pending_queued'
                            ],
                            [
                                "id" =>  $coderw_id,
                                "value" => 1
                            ], [
                                "id" =>  $checkmark_id,
                                "value" => 'checked'
                            ]
                        ],
                    ];

                    $pyrus->addComment($task_id, $data);
                } else {
                    Log::channel('daily')->warning('Заказ №' . $task_id . ' не собран для списания');
                    $data = [
                        'formatted_text' => 'Ошибка. Ответ от Reworker : ' . json_encode($try, JSON_UNESCAPED_UNICODE)
                    ];
                    $pyrus->addComment($task_id, $data);
                }
            }
        }

        if ($rp->getStatusrw_value($task_fields) == 'complete' && $rp->get1Cnumber_value($task_fields) == null) {

            $query = (new OneCWriteOff)->getQuery($task_id, $test);
            $data = [
                'formatted_text' => 'Отправлено в 1С ' . json_encode($query, JSON_UNESCAPED_UNICODE)
            ];
            $pyrus->addComment($task_id, $data);
            $tyy = (new OneC())->WrtiteOff($query);
            $data = [
                'formattes_text' => 'Ответ 1С: ' . json_encode($tyy, JSON_UNESCAPED_UNICODE)
            ];
            $pyrus->addComment($task_id, $data);
            if ($tyy['ВсеХорошо'] === true) {
                $data = [
                    'formatted_text' => 'Cтатус 1С : Успешно',
                    'field_updates' => [[
                        "id" =>  $C_status,
                        "value" => 'Успешно'
                    ], [
                        "id" =>  $C_number,
                        "value" => $tyy['Результат']['НомерДокумента'],
                    ]]
                ];
                $pyrus->addComment($task_id, $data);
            }

            return $tyy;
        }
    }
}
