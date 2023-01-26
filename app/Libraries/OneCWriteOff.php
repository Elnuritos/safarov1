<?php

namespace  App\Libraries;


use Illuminate\Support\Facades\Http;
use GuzzleHttp\HandlerStack;

class OneCWriteOff
{
    public function getQuery($task_id, $test)
    {
        $carr = [];
        foreach ($test as $key => $value) {
            $cproducts = [
                "Код1С" => '',
                "Количество" => '',
            ];
            $cproducts['Код1С'] = $value['Код товара'];
            $cproducts['Количество'] = (string)$value['Кол-во'];
            array_push($carr, $cproducts);
        }
        $cdata = [
            "ДатаДокумента" => date('YmdHis'),
            "НомерЗаявки" => $task_id,
            "ИННОрганизации" => "770475035048",
            "КодСклада" => "БП-000003",
            "ТабличнаяЧасть" => $carr,
        ];
        $query = [
            'url' => "http://1cserverhz/Buh_CORP_HTTPService/hs/SformirovatSpisanie",
            'data' => $cdata
        ];
        return $query;
    }
}
