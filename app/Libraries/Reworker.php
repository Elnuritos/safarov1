<?php

namespace  App\Libraries;


use Illuminate\Support\Facades\Http;



class Reworker
{
    public function __construct()
    {
        $this->vars = [
            'Token' => 'iHI5tPRmmIyQHfeFti59',
        ];
    }
    public function GetRw($code)
    {

        $res = Http::withHeaders($this->vars)->get(getenv('REWORKER_PRODUCT_ARTICLE') . $code)->json();
        return $res;
    }

    public function createOrder($data)
    {
        $path = "/api/products/order";

        $request = $this->query()->post(env('ORDER_ADMIN_API_URL') . $path, $data)->json();

        return $request;
    }

    public function query()
    {
        return Http::withBasicAuth(env('ORDER_ADMIN_USER'), env('ORDER_ADMIN_PASS'))->withHeaders([
            'Accept' => 'application/json',
            'Content-type' => 'application/json'
        ]);
    }
    public function getOrderByExtID($ext_order_id)
    {
        $query = [
            "filter" => [
                [
                    "field" => "extId",
                    "type" => "eq",
                    "value" => $ext_order_id
                ]
            ]
        ];

        $path = "/api/products/order?" . http_build_query($query);

        $result = $this->query()->get(env('ORDER_ADMIN_API_URL') . $path);

        return json_decode($result, true);
    }
    public function getStatusByExtID($id)
    {
        $status =  $this->getOrderByExtID($id)['_embedded']['order'][0]['state'];
        return $status;
    }
}
