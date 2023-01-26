<?php

namespace App\Libraries;

use App\Models\InsalesProducts;
use App\Models\InsalesUpdateOrders;
use App\Services\Actions\ActionInterface;
use Insales\Library\Insales as ApiInsales;

class UpdateInsalesReworker implements ActionInterface
{
    public $insales_id;
    public $rw_id;

    public function __construct($insales_id, $rw_id)
    {
        $this->insales_id = $insales_id;
        $this->rw_id = $rw_id;

    }

    public function run()
    {

        $url = "https://323f9baffa77dfd407d9ee42731e3d7a:14aab7f0d7a89a0c0aa52dd4d54fc29c@dimaestri.com";
        $client = new ApiInsales($url);
        $rw_order = (new GetReworkerOrder)->getorder($this->rw_id);

        $res_table = (new GetInsalesOrder)->getorder($this->insales_id);
        $order_line_id = (new GetInsalesOrder)->getorderline($this->insales_id);


        $res = [
            "order" => [
                "order_lines_attributes" => [],
            ]
        ];

        if ($rw_order != $res_table) {
            //remove
            foreach ($order_line_id as $key => $value) {
                $order_line_delete_arr =
                    [
                        "id" => $value["id"],
                        "_destroy" => true,

                    ];
                array_push($res['order']['order_lines_attributes'], $order_line_delete_arr);
            }
            $remove = $client->removeOrderLine($this->insales_id, $res)->getData();
            // $insales_update = ["order" => ["order_lines_attributes" => []]];
            foreach ($rw_order as $key => $value) {
                $insales_update = ["order" => ["order_lines_attributes" => []]];
                $res_order = [

                    "product_id" => InsalesProducts::where('article', $value['article'])->value('product_id'),
                    "title" => InsalesProducts::where('article', $value['article'])->value('title'),
                    "sale_price" => $value['price'],
                    "quantity" => $value['quantity'],

                ];
                array_push($insales_update['order']['order_lines_attributes'], $res_order);

                //   $unique[0]->put('title',$title);
                $cr = $client->createOrderLineByProduct($this->insales_id, $insales_update)->getResponse();
                if ($cr['message'] == "OK") {
                    InsalesUpdateOrders::create([
                        "article" => $value['article'],
                        "exported" => 1,
                        "title" => $res_order["title"],
                        "price" => $res_order['sale_price'],
                    ]);
                } else {
                    InsalesUpdateOrders::create([
                        "article" => $value['article'],
                        "exported" => 0,
                        "title" => $res_order["title"],
                        "price" => $res_order['sale_price'],
                    ]);
                }
            }
        }
    }
}
