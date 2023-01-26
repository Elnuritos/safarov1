<?php

namespace App\Libraries;

use Insales\Library\Insales as ApiInsales;


class GetInsalesOrder
{
    public function getorder($insales_id)
    {
        $url = "https://323f9baffa77dfd407d9ee42731e3d7a:14aab7f0d7a89a0c0aa52dd4d54fc29c@dimaestri.com";
        $client = new ApiInsales($url);
        $order = $client->getOrderById($insales_id)->getResponse();
        $order_lines = \SplFixedArray::fromArray($order['data']['order_lines']);
        $iterator1 = $order_lines->getIterator();
        $res_table = [];
        while ($iterator1->valid()) {
            $table_info = [
                'article' => '',
                'price' => '',
                'quantity' => '',

            ];
            $table_info['price'] = $iterator1->current()['sale_price'];
            $table_info['quantity'] = $iterator1->current()['quantity'];
            $table_info['article'] = $iterator1->current()['sku'];
            array_push($res_table, $table_info);

            $iterator1->next();
        }
        return $res_table;
    }
    public function getorderline($insales_id)
    {
        $url = "https://323f9baffa77dfd407d9ee42731e3d7a:14aab7f0d7a89a0c0aa52dd4d54fc29c@dimaestri.com";
        $client = new ApiInsales($url);
        $order = $client->getOrderById($insales_id)->getResponse();
        $order_lines = \SplFixedArray::fromArray($order['data']['order_lines']);
        $iterator1 = $order_lines->getIterator();
        $order_line_id = [];
        while ($iterator1->valid()) {
            $ids = [
                "id" => '',
            ];
            $ids['id'] = $iterator1->current()['id'];
            array_push($order_line_id, $ids);
            $iterator1->next();
        }
        return $order_line_id;
    }
}
