<?php

namespace  App\Libraries;


use Illuminate\Support\Facades\Http;
use GuzzleHttp\HandlerStack;

class MpStats
{
    public array $vars;

    public function __construct()
    {
        $this->vars = [
            'X-Mpstats-TOKEN' => '630477dc559682.03020369a697cf1d62bfa12d081c53e51e825fba',

        ];
    }
    public function getItemLink($ids)
    {
        $url = 'https://mpstats.io/api/wb/get/items/batch';
        $res = Http::withHeaders($this->vars)->post($url, $ids)->json();

        foreach ($res as $key => $v1) {
            foreach ($v1 as $key => $v2) {
                if ($key == 'item') {

                    $result[] = $v1['item']['link'];
                }
            }
        }
        return $result;
    }

    public function getLink($arr)
    {

        $res1 =  ['ids' => []];
        $res2 =  ['ids' => []];
        $n = [];


        $counter = 1;

        for ($i = 0; $i < count($arr['ids']); $i++) {
            array_push($res1['ids'], $arr['ids'][$i]);
            if ($i / 199 == $counter) {

                $json = $this->getItemLink($res1);
                $res1 =  ['ids' => []];
                array_push($n, $json);
                sleep(5);

                $counter += 1;
            }
            if (count($arr['ids']) - 199 * ($counter - 1) < 200) {

                array_push($res2['ids'], $arr['ids'][$i]);
            }
        }

        $json2 = $this->getItemLink($res2);
        array_push($n, $json2);
        return $n;
    }
}
