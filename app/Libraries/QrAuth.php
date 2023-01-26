<?php

namespace  App\Libraries;



class QrAuth
{
    public function __construct()
    {
        $client_id_secret = base64_encode(getenv('QR_CLIENT_ID') . ':' . getenv('QR_CLIENT_SECRET'));


        $RqUID = uniqid() . uniqid() . uniqid();
        $res = substr($RqUID, 0, -9) . "1B";
        //  $RqUID = '25Ec70328e2CE4DF39e828E1dF85EFa0';
        // dd($res);


        $this->vars = [
            'authorization' => ' Basic ' . $client_id_secret,
            'RqUID' => $res,

        ];
    }

    public function auth($scope)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.sberbank.ru:8443/prod/tokens/v2/oauth");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("authorization: {$this->vars['authorization']}", "RqUid: {$this->vars['RqUID']}", "accept:application/json", "content-type:application/x-www-form-urlencoded; charset=UTF-8"));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('grant_type' => 'client_credentials', 'scope' => $scope)));
        curl_setopt($ch, CURLOPT_SSLCERT, storage_path('/certificate_019ec6d2-d3f6-4b00-a411-20863b4878df.p12')); //поменять путь
        curl_setopt($ch, CURLOPT_SSLKEYPASSWD,  "2k5t4U@!PnEhgxUL");
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, "p12");
        $result = curl_exec($ch);

        if (curl_errno($ch)) {

            return curl_errno($ch);
        }
        //   dd($result);
        curl_close($ch);

        $result = json_decode($result, true);
        $rq = [
            'RqUID' => $this->vars['RqUID']
        ];
        array_push($result,  $rq);

        return $result;
    }
}
