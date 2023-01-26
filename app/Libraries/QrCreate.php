<?php

namespace  App\Libraries;


use App\Libraries\QrAuth;



class QrCreate
{




    public function create($task_id, $tokens)
    {
        /*  $token = (new QrAuth)->auth($token); */
        $pyrusSber = new PyrusSberForm();
        $rq_id = $tokens[0]['RqUID'];

        $token = $tokens['access_token'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.sberbank.ru:8443/prod/qr/order/v3/creation");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $member_id = '00002085';
        $rq_uid =  $rq_id;
        $rq_tm =  date("Y-m-d") . 'T' . date("H:i:s")  . 'Z';
        $order_number = $task_id . '1';
        $order_create_date =  date("Y-m-d") . 'T' . date("H:i:s") . 'Z';
        //     dd($rq_tm);
        $pyrus = new Pyrus();
        $task_fields = $pyrus->getTaskFields($task_id);
        $ship = $pyrusSber->getShipment($task_fields);
        $bonus = $pyrusSber->getBonus($task_fields);
        $money = $pyrusSber->getFieldValue($task_fields);
        $tovar = $pyrusSber->getTable($task_fields);
        $upd = $pyrusSber->getUpd($task_fields);
        if ($upd != 0) {
            $money += $upd;
        }
        $items = $pyrusSber->getItems($tovar, $ship, $bonus, $money, $upd);
        $res = [];
        foreach ($items as $key => $v) {
            $order_params_type = [
                "position_name" => '',
                "position_count" => 0,
                "position_sum" => '',
                "position_description" => ''
            ];
            $order_params_type['position_name'] = addcslashes($v['name'], ',');

            $order_params_type['position_count'] = (int)$v['quantity']['value'];
            $order_params_type['position_sum'] = $v['itemPrice'];
            $order_params_type['position_description'] = addcslashes($v['name'], ',');
            array_push($res, $order_params_type);
        }

        $id_qr = '25843881';
        $order_sum = $money * 100;
        $currency = '643';
        $description = 'Кофе в капсулах';
        $sbp_member_id = '100000000111';

        $fields = [
            'rq_uid' => $rq_uid,
            'rq_tm' => $rq_tm,
            'member_id' => $member_id,
            'order_number' => $order_number,
            'order_create_date' => $order_create_date,
            //     'order_params_type' => $res,
            'id_qr' => $id_qr,
            'order_sum' => $order_sum,
            'currency' => $currency,
            'description' => $description,
            'sbp_member_id' => $sbp_member_id
        ];
        //urldecode(http_build_query($fields))
        //   dd($fields);
        //   dd(json_encode($fields, JSON_UNESCAPED_UNICODE));
        //   dd(urldecode(http_build_query($fields)));
        //   dd(array("authorization:Bearer {$token}", "RqUID: { $rq_id}", "content-type:application/json"));
        // dd(urldecode(http_build_query($fields)));

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("authorization:Bearer {$token}", "RqUID: {$rq_id}", "Content-Type: application/json"));
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
        // dd($result, json_encode($fields, JSON_UNESCAPED_UNICODE));
    }
}
