<?php

namespace App\Libraries;

use App\Libraries\Pyrus;
use App\Models\Insales;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Insales\Library\Insales as ApiInsales;

class InsalesPyrus
{
    private function makeLinkInCRM(int $task_id, int $order_id)
    {
        $res = Http::post(getenv('CRM_API_URL'), [
            'action' => 'Site/createOrderTaskLink',
            'task_id' => $task_id,
            'order_id' => $order_id,
        ])->json();
        return $res;
    }
    public function getInsales()
    {

        $pyrus = new Pyrus;
        // dd(Insales::all());
        // Insales::turnicate();
        $url = "https://323f9baffa77dfd407d9ee42731e3d7a:14aab7f0d7a89a0c0aa52dd4d54fc29c@dimaestri.com";
        $client = new ApiInsales($url);
        $order = $client->getOrders(['per_page' => 50])->getResponse();
        if ($order['data'] != null) {

            $arrayobject = \SplFixedArray::fromArray($order['data']);

            $iterator = $arrayobject->getIterator();
            while ($iterator->valid()) {



                    if ($iterator->current()['discounts'] != null) {
                        foreach ($iterator->current()['discounts'] as $key => $value) {
                            if (str_contains($value['description'], "Скидка по купону ")) {
                                if ((int) $value['amount'] == 5000) {
                                    $choice_id = 1;
                                }
                                if ((int) $value['amount'] == 10000) {
                                    $choice_id = 2;
                                }
                                if ((int) $value['amount'] == 15000) {
                                    $choice_id = 3;
                                }
                                $discount_arr = [
                                    'row_id' => 0,
                                    'cells' => [
                                        [
                                            'id' => 227,
                                            'value' => str_replace("Скидка по купону ", "", $value['description']),
                                        ],
                                        [
                                            'id' => 228,
                                            "value" => [
                                                'choice_id' => $choice_id,
                                            ],
                                        ],

                                    ],
                                ];
                            }

                        }

                    }
                    $res_table = [];
                    $res_client = [];
                    $order_lines = \SplFixedArray::fromArray($iterator->current()['order_lines']);
                    $iterator1 = $order_lines->getIterator();
                    while ($iterator1->valid()) {
                        $table_info = [
                            'price' => '',
                            'quantity' => '',
                            'title' => '',
                            'total_price' => '',
                            'sku' => '',
                        ];
                        $table_info['price'] = $iterator1->current()['sale_price'];
                        $table_info['quantity'] = $iterator1->current()['quantity'];
                        $table_info['title'] = $iterator1->current()['title'];
                        $table_info['total_price'] = $iterator1->current()['total_price'];
                        $table_info['sku'] = $iterator1->current()['sku'];
                        array_push($res_table, $table_info);
                        $iterator1->next();
                    }

                    if (isset($iterator->current()['discounts'][0]['full_amount'])) {

                        $discount = $iterator->current()['discounts'][0]['full_amount'];
                    } else {
                        $discount = null;
                    }
                    $total_price = $iterator->current()['total_price'];
                    $name = $iterator->current()['client']['name'];
                    $payment_title = $iterator->current()['payment_title'];
                    $delivery_title = $iterator->current()['delivery_title'];
                    $delivery_price = (int) $iterator->current()['delivery_price'];
                    $comment = $iterator->current()['comment'];
                    $items_price = $iterator->current()['items_price'] + $discount;

                    $phone = $iterator->current()['client']['phone'];

                    if (substr($phone, 0, 1) == "8") {
                        $phone = ltrim($phone, '8');
                        $phone = "+7" . $phone;
                    }
                    $email = $iterator->current()['client']['email'];
                    $adress = $iterator->current()['shipping_address']['full_delivery_address'];
                    $count = 0;
                    $ptable = [];
                    foreach ($res_table as $key => $value) {
                        $table1 = [
                            'row_id' => $key,
                            'cells' => [
                                [
                                    'id' => 88,
                                    //'id' => 16,
                                    'value' => $value['title'],
                                ],
                                [
                                    'id' => 89,
                                    //'id' => 17,
                                    'value' => $value['sku'],
                                ],
                                [
                                    'id' => 90,
                                    //'id' => 18,
                                    'value' => $value['quantity'],
                                ],
                                [
                                    'id' => 91,
                                    //'id' => 19,
                                    'value' => $value['price'],
                                ],
                                [
                                    'id' => 92,
                                    //'id' => 20,
                                    'value' => $value['total_price'],
                                ],
                            ],
                        ];
                        $count += 1;
                        array_push($ptable, $table1);
                    }
                    $count = 0;
                    $xtable = [];
                    $cat1 = $pyrus->getCatalog(140388);

                    $arrik = \SplFixedArray::fromArray($cat1['items']);
                    $iterator2 = $arrik->getIterator();
                    $i = 0;
                    $gg = [];

                    foreach ($res_table as $key => $value) {
                        while ($iterator2->valid()) {
                            if ($iterator2->current()['values'][2] == $value['sku']) {

                                $arr = [
                                    'name' => $iterator2->current()['item_id'],
                                    'rrc' => $iterator2->current()['values'][17],
                                ];
                                array_push($gg, $arr);
                            }
                            $iterator2->next();
                        }
                        $iterator2 = $arrik->getIterator();
                    }

                    $cat2 = $pyrus->getCatalog(146127);
                    $arrik1 = \SplFixedArray::fromArray($cat2['items']);
                    $iterator3 = $arrik1->getIterator();
                    $i = 0;
                    $ostatok = [];
                    while ($iterator3->valid()) {
                        if ($iterator3->current()['values'][2] == isset($res_table[$i]['sku'])) {
                            $i += 1;
                            $arr = [
                                'ost' => $iterator3->current()['values'][3],
                            ];
                            array_push($ostatok, $arr);
                        }

                        $iterator3->next();
                    }

                    foreach ($res_table as $key => $value) {

                        $table1 = [
                            'row_id' => $count,
                            'cells' => [
                                [
                                    'id' => 120,
                                    //'id' => 42,
                                    'value' => [
                                        "item_id" => $gg[$count]['name'],
                                    ],
                                ],

                                [
                                    'id' => 138,
                                    //'id' => 47,
                                    'value' => $gg[$count]['rrc'],
                                ],
                                [
                                    'id' => 161,
                                    //'id' => 46,
                                    'value' => $ostatok[$count]['ost'],
                                ],
                                [
                                    'id' => 107,
                                    //'id' => 48,
                                    'value' => $res_table[$count]['sku'],
                                ],
                                [
                                    'id' => 103,
                                    //'id' => 44,
                                    'value' => $res_table[$count]['quantity'],
                                ],
                                [
                                    'id' => 104,
                                    //'id' => 43,
                                    'value' => $res_table[$count]['price'],
                                ],

                            ],
                        ];
                        $count += 1;
                        array_push($xtable, $table1);
                    }

                    $order_id = strval($iterator->current()['id']);
                    /* dd($xtable, $order_id); */
                    $pyrus_data = [
                        "form_id" => 1046999,
                        //"form_id" =>   1124748,
                        'subject' => 'test',
                        "fields" => [
                            [
                                "id" => 123,
                                //"id" => 3,
                                "value" => $phone,
                            ],
                            [
                                "id" => 149,
                                //"id" => 2,
                                "value" => $name,
                            ],
                            [
                                "id" => 150,
                                //"id" => 4,
                                "value" => $email,
                            ],
                            [
                                "id" => 115,
                                //"id" => 5,
                                "value" => [
                                    'id' => 332392,
                                ],

                            ],
                            [
                                "id" => 48,
                                //"id" => 7,
                                "value" => [
                                    'choice_id' => 1,
                                ],
                            ],
                            [
                                "id" => 55,
                                // "id" => 7,
                                "value" => [
                                    'choice_id' => 1,
                                ],
                            ],

                            [
                                "id" => 49,
                                //"id" => 8,
                                "value" => [
                                    'choice_id' => 1,
                                ],
                            ],
                            [
                                "id" => 87,
                                //"id" => 11,
                                "value" => $ptable,
                            ],
                            [
                                "id" => 95,
                                //"id" => 12,
                                "value" => $payment_title,
                            ],
                            [
                                "id" => 93,
                                //"id" => 13,
                                "value" => $delivery_title,
                            ],
                            [
                                "id" => 85,
                                //"id" => 14,
                                "value" => $adress,
                            ],

                            [
                                'id' => 101,
                                //'id' => 33,
                                "value" => $xtable,
                            ],

                            [
                                'id' => 121,
                                //'id' => 40,
                                "value" => $total_price,
                            ],
                            [
                                'id' => 151,
                                //'id' => 35,

                                "value" => $discount,
                            ],
                            [
                                'id' => 94,
                                //'id' => 36,
                                "value" => (int) $delivery_price,
                            ],
                            [
                                'id' => 122,
                                //'id' => 34,

                                "value" => $items_price,
                            ],
                            [
                                'id' => 218,
                                //'id' => 100,

                                "value" => $order_id,
                            ],

                        ],
                    ];
                    if ($comment) {
                        $com =
                            [
                            "id" => 97,
                            //"id" => 15,
                            "value" => $comment,
                        ];
                        array_push($pyrus_data['fields'], $com);
                    }
                    if (isset($discount_arr)) {
                        $dis =
                            [
                                "id" => 225,
                                "value" => "checked",
                            ];
                            array_push($pyrus_data['fields'], $dis);
                        $dat=
                            [
                            "id" => 226,
                            "value" =>[$discount_arr],
                            ];
                            array_push($pyrus_data['fields'], $dat);


                }


                    $tryd = (new Pyrus)->addDefaultData($pyrus_data);
                    $formatted_data = [
                        "form_id" => 1046999,
                        //  "form_id" => 1124748,
                        "fields" => $tryd,

                    ];
                    if (Insales::where('order_id', $order_id)->first() == null) {
                        try {
                            $task = (new Pyrus())->createTask($formatted_data);
                        } catch (\Exception$e) {
                            Log::channel('daily')->warning("Не удалось провести  создание заказа с инсейлза {$order_id}", ['msg' => $e]);
                        }

                        //  $task = (new Pyrus())->createTask($pyrus_data);
                        $task_id = $task['task']['id'];
                        Insales::Create([
                            'order_id' => $order_id,
                            'task_id' => $task_id,
                        ]);
                        $error = [
                            'order_id' => $order_id,
                            'task_id' => $task_id,
                        ];
                        if ($payment_title == "Оплата онлайн") {
                            $data = [
                                'field_updates' => [[
                                    "id" => 99,
                                    "value" => [
                                        'choice_id' => 3,
                                    ],
                                ]],
                            ];
                            $comment = $pyrus->addComment($task_id, $data);

                            Redis::LPUSH('online_payment_insales', json_encode($error));
                        }
                        try {
                            $response = $this->makeLinkInCRM($task_id, $order_id);
                            $values = explode(", ", $response['response']);

                            if (!array_key_exists('response', $response) || empty($values[0]) == true || empty($values[1]) == true) {
                                throw new \Exception("Срм выдал ошибку");
                            }
                        } catch (\Exception$e) {
                            Redis::LPUSH('crm_insales_errors', json_encode($error));
                        }

                    }

                $iterator->next();
            }
        }
        return 'ok';
    }
}
