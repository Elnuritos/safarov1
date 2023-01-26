<?php

namespace  App\Libraries;

use Reworker\Factory as Ha;
use App\Libraries\Pyrus;

use GuzzleHttp\HandlerStack;
use App\Models\InsalesProducts;
use App\Models\InsalesUpdateOrders;
use Illuminate\Support\Facades\Http;


class GetReworkerOrder
{
    public function getorder($rw_id)
    {
        $url = ['base_uri' => "http://195.161.69.22:4444/api/reworker-products/articles/"];
        $testrw = new Ha();
        $rw = $testrw->init(getenv('ORDER_ADMIN_USER'), getenv('ORDER_ADMIN_PASS'));
        $cat_treit = (new Pyrus)->getCatalog(149516);
        $items = $rw->orders->getOrderProducts($rw_id);
        $rw_order = [];
        $arr_rw = \SplFixedArray::fromArray($items);
        $iterator_rw = $arr_rw->getIterator();
        $word = "ТРЕЙБОКС";
        $treit = \SplFixedArray::fromArray($cat_treit['items']);
        $iterator_treit = $treit->getIterator();
        while ($iterator_rw->valid()) {
            $arr = [
                'article' => '',
                'price' => '',
                'quantity' => '',
            ];
            if (strpos($iterator_rw->current()['productOfferRaw']['name'], $word) !== false) {
                while ($iterator_treit->valid()) {
                    if ($iterator_rw->current()['productOfferRaw']['article'] == $iterator_treit->current()['values'][0]) {
                        $arr = [
                            'article' => $iterator_treit->current()['values'][2],
                            'price' => $iterator_rw->current()['price'] / ($iterator_treit->current()['values'][4] * $iterator_rw->current()['count']),
                            'quantity' => $iterator_treit->current()['values'][4] * $iterator_rw->current()['count'],
                        ];
                    }
                    $iterator_treit->next();
                }
            } else {

                $arr = [
                    'article' => $iterator_rw->current()['productOfferRaw']['article'],
                    'price' => $iterator_rw->current()['price'],
                    'quantity' => $iterator_rw->current()['count'],
                ];
            }

            array_push($rw_order, $arr);
            $iterator_rw->next();
            $iterator_treit = $treit->getIterator();
        }
        return $rw_order;
    }
}
