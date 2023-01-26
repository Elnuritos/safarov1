<?php

namespace  App\Libraries;


use App\Libraries\QrAuth;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Http;


class QrStatus
{




    public function status($task_id, $tokens, $order_id)
    {

        $rq_id = $tokens[0]['RqUID'];
        $token = $tokens['access_token'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.sberbank.ru:8443/prod/qr/order/v3/status");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("authorization:Bearer {$token}", "RqUID: {$rq_id}", "Content-Type: application/json"));
        $tid = '25843881';
        $rq_tm =  date("Y-m-d") . 'T' . date("H:i:s")  . 'Z';

        $fields = [
            'rq_uid' => $rq_id,
            'rq_tm' => $rq_tm,
            'order_id' => $order_id,
            'tid' =>  $tid,
            'partner_order_number' => (string) $task_id,
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_SSLCERT, storage_path('/certificate_019ec6d2-d3f6-4b00-a411-20863b4878df.p12'));
        curl_setopt($ch, CURLOPT_SSLKEYPASSWD,  "2k5t4U@!PnEhgxUL");
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, "p12");
        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            return curl_errno($ch);
        }

        curl_close($ch);

        $result = json_decode($result, true);

        return $result;
    }
}
