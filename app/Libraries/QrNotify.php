<?php

namespace  App\Libraries;


use Illuminate\Support\Facades\Http;
use GuzzleHttp\HandlerStack;


class QrNotify
{
    //ask sber after 10 days
    public function notify()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.sberbank.ru:8443/prod/qr/order/v3/creation");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $rq_tm =  date("Y-m-d") . 'T' . date("H:i:s")  . 'Z';
        $member_id = '00002085';
        $tid = '25843881';
        $fields = [
            "rqUid" => "",
            "rqTm" =>  $rq_tm,
            "memberId" => $member_id,
            "tid" =>  $tid,
            "orderId" => "bb072868e59e4f06a5ecbc44baa0e63c",
            "partnerOrderNumber" => "190331500624",
            "orderState" => "REFUNDED",
            "operationId" => "767fa5f8d7aa4f0fad504bea782518f8",
            "operationDateTime" => "2020-03-19T19:00:39Z",
            "operationType" => "REFUND",
            "responseCode" => "00",
            "rrn" => "004207370593",
            "operationSum" => 800,
            "operationCurrency" => "643",
            "authCode" => "370694",
            "responseDesc" => "ResponseDesc",
            "clientName" => "Иван Иванович И."
        ];
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_SSLCERT, "../certificate_019ec6d2-d3f6-4b00-a411-20863b4878df.p12");
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
