<?php

namespace  App\Libraries;


use Illuminate\Support\Facades\Http;


class Wb
{
    public function getItem($id)
    {
        $url = "https://wbx-content-v2.wbstatic.net/ru/{$id}.json";
        $res = Http::get($url)->json();
        return $res;
    }
    public function getPackageType($data)
    {

        foreach ($data['options'] as $key => $v1) {
            foreach ($v1 as $key => $value) {

                if ($value == "Количество капсул/пакетиков") {

                    return $v1['value'];
                }
            }


            /*
            if (isset($value['ao_id'])) {
                if ($value['ao_id'] == 233701 || $value['ao_id'] == 179792) {

                    return $value['value'];
                } else {
                    dd('s');
                    return 'нет поля';
                }
            } else {
                return 'нет поля вообще';
            } */
        }
    }
}
