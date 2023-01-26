<?php

namespace App\Console\Commands;

use App\Libraries\Pyrus;
use App\Models\OzonFboOrders;
use Illuminate\Console\Command;
use Ozon\Library\Ozon as ApiOzon;
use Illuminate\Support\Facades\Log;
use App\Models\OzonFboOrdersProducts;
use App\Models\OzonFboOrdersAnaliticsData;

class OzonDbFbo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:ozon_fbo_db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = "https://api-seller.ozon.ru";
        $client = new ApiOzon($url);
        $headers = [
            0 => "Client-Id:9111",
            1 => "Api-Key:c4a1808a-c275-469f-9ef6-2e2bb7730d90",
        ];
        $i = 0;
        /*  OzonFboOrdersAnaliticsData::truncate();
        OzonFboOrders::truncate();
        OzonFboOrdersProducts::truncate();
        dd('s'); */
        $cat1 = (new Pyrus)->getCatalog(140388);
        do {
            $jayParsedAry = [
                "dir" => "ASC",
                "filter" => [
                    "since" => "2022-12-09T00:00:00.000Z",
                    "to" => date("Y-m-d") . 'T' . date("H:i:s") . 'Z',
                ],
                "limit" => 20,
                "offset" => $i,
                "translit" => true,
                "with" => [
                    "analytics_data" => true,
                    "financial_data" => true,
                ],
            ];

            $fbs = $client->getFboList($jayParsedAry, $headers)->getResponse();

            $arrayobject = \SplFixedArray::fromArray($fbs['data']['result']);
            $iterator = $arrayobject->getIterator();
            while ($iterator->valid()) {
                $str = array("T", "Z");
                $in_process_at = str_replace($str, " ", $iterator->current()['in_process_at']);

                $created_at_ozon = str_replace($str, " ", $iterator->current()['created_at']);

                if ($created_at_ozon == null) {
                    $created_at_ozon = null;
                }

                if (OzonFboOrdersProducts::where('order_id', $iterator->current()['order_id'])->first() == null) {
                    foreach ($iterator->current()['products'] as $key => $value) {

                        $arrayobject1 = \SplFixedArray::fromArray($cat1['items']);
                        $iterator1 = $arrayobject1->getIterator();
                        while ($iterator1->valid()) {
                            if ($value['offer_id'] == $iterator1->current()['values'][21]) {
                                // dd($value['offer_id']);
                                OzonFboOrdersProducts::create([

                                    "order_id" => $iterator->current()['order_id'],
                                    "price" => $value['price'],
                                    "article" => $iterator1->current()['values'][2],
                                    "name" => $value['name'],
                                    "sku" => $value['sku'],
                                    "quantity" => $value['quantity'],
                                    "ProductTotal" => (int) $value['quantity'] * (int) $value['price'],
                                    "offer_id" => $value['offer_id'],
                                ]);
                            }

                            $iterator1->next();
                        }
                        $iterator1 = $arrayobject1->getIterator();

                    }
                }

                if (OzonFboOrders::where('order_id', $iterator->current()['order_id'])->first() == null) {
                    OzonFboOrders::create([
                        "posting_number" => $iterator->current()['posting_number'],
                        "status" => $iterator->current()['status'],
                        "order_id" => $iterator->current()['order_id'],
                        "order_number" => $iterator->current()['order_number'],
                        "in_process_at" => $in_process_at,
                        "created_at_ozon" => $created_at_ozon,
                    ]);
                }
                if (OzonFboOrdersAnaliticsData::where('order_id', $iterator->current()['order_id'])->first() == null) {
                    OzonFboOrdersAnaliticsData::create([

                        "warehouse_id" => $iterator->current()['analytics_data']['warehouse_id'],
                        "order_id" => $iterator->current()['order_id'],
                        "region" => $iterator->current()['analytics_data']['region'],
                        "city" => $iterator->current()['analytics_data']['city'],
                        "payment_type_group_name" => $iterator->current()['analytics_data']['payment_type_group_name'],
                        "warehouse_name" => $iterator->current()['analytics_data']['warehouse_name'],
                        "delivery_type" => $iterator->current()['analytics_data']['delivery_type'],

                    ]);

                }

                $iterator->next();
            }
            $i += 20;

        } while ($i < 6500);

        $memory = memory_get_usage();
        Log::channel('daily')->info("Данные по озон fbo записались в бд, выделилось памяти - ".$memory);
        return $memory;
    }
}
