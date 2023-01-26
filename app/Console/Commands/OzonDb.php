<?php

namespace App\Console\Commands;

use App\Libraries\Pyrus;
use App\Models\OzonOrders;
use Illuminate\Console\Command;
use Ozon\Library\Ozon as ApiOzon;
use App\Models\OzonOrdersProducts;
use Illuminate\Support\Facades\Log;
use App\Models\OzonOrdersAnaliticsData;
use App\Models\OzonOrdersCancellations;
use App\Models\OzonOrdersDeliveryMethod;

class OzonDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:ozon_db';

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

        $memory = memory_get_usage();

        $url = "https://api-seller.ozon.ru";
        $client = new ApiOzon($url);
        $headers = [
            0 => "Client-Id:9111",
            1 => "Api-Key:c4a1808a-c275-469f-9ef6-2e2bb7730d90",
        ];
        $i = 0;
        $cat1 = (new Pyrus)->getCatalog(140388);
/*     OzonOrdersCancellations::truncate();
OzonOrdersDeliveryMethod::truncate();
OzonOrdersAnaliticsData::truncate();
OzonOrders::truncate(); */

        do {
            $jayParsedAry = [
                "dir" => "ASC",
                "filter" => [
                    "since" => "2022-12-10T00:00:00.000Z",
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

            $fbs = $client->getFbsList($jayParsedAry, $headers)->getResponse();
            $arrayobject = \SplFixedArray::fromArray($fbs['data']['result']['postings']);
            $iterator = $arrayobject->getIterator();
            while ($iterator->valid()) {
                $str = array("T", "Z");
                $in_process_at = str_replace($str, " ", $iterator->current()['in_process_at']);
                $shipment_date = str_replace($str, " ", $iterator->current()['shipment_date']);
                $delivering_date = str_replace($str, " ", $iterator->current()['delivering_date']);
                $delivery_date_begin = str_replace($str, " ", $iterator->current()['analytics_data']['delivery_date_begin']);
                $delivery_date_end = str_replace($str, " ", $iterator->current()['analytics_data']['delivery_date_end']);
                if ($delivering_date == null) {
                    $delivering_date = null;
                }
                if ($delivery_date_begin == null) {
                    $delivery_date_begin = null;
                }
                if ($delivery_date_end == null) {
                    $delivery_date_end = null;
                }

                if (OzonOrdersProducts::where('order_id', $iterator->current()['order_id'])->first() == null) {
                    foreach ($iterator->current()['products'] as $key => $value) {

                        $arrayobject1 = \SplFixedArray::fromArray($cat1['items']);
                        $iterator1 = $arrayobject1->getIterator();
                        while ($iterator1->valid()) {


                                if ($value['offer_id'] == $iterator1->current()['values'][21]) {

                                    OzonOrdersProducts::create([
                                        "order_id" => $iterator->current()['order_id'],
                                        "price" => (int)$value['price'],
                                        "article" => $iterator1->current()['values'][2],
                                        "name" => $value['name'],
                                        "sku" => $value['sku'],
                                        "quantity" => $value['quantity'],
                                        "ProductTotal" => (int) $value['quantity'] * (int) $value['price'],
                                    ]);
                                }



                            $iterator1->next();
                        }
                        $iterator1 = $arrayobject1->getIterator();

                    }
                }

                if (OzonOrders::where('order_id', $iterator->current()['order_id'])->first() == null) {
                    OzonOrders::create([
                        "posting_number" => $iterator->current()['posting_number'],
                        "status" => $iterator->current()['status'],
                        "order_id" => $iterator->current()['order_id'],
                        "order_number" => $iterator->current()['order_number'],
                        "tracking_number" => $iterator->current()['tracking_number'],
                        "tpl_integration_type" => $iterator->current()['tpl_integration_type'],
                        "in_process_at" => $in_process_at,
                        "shipment_date" => $shipment_date,
                        "delivering_date" => $delivering_date,
                    ]);
                }
                if (OzonOrdersAnaliticsData::where('order_id', $iterator->current()['order_id'])->first() == null) {

                    OzonOrdersAnaliticsData::create([

                        "warehouse_id" => $iterator->current()['analytics_data']['warehouse_id'],
                        "tpl_provider_id" => $iterator->current()['analytics_data']['tpl_provider_id'],
                        "order_id" => $iterator->current()['order_id'],
                        "region" => $iterator->current()['analytics_data']['region'],
                        "city" => $iterator->current()['analytics_data']['city'],
                        "payment_type_group_name" => $iterator->current()['analytics_data']['payment_type_group_name'],
                        "warehouse" => $iterator->current()['analytics_data']['warehouse'],
                        "tpl_provider" => $iterator->current()['analytics_data']['tpl_provider'],
                        "delivery_date_begin" => $delivery_date_begin,
                        "delivery_date_end" => $delivery_date_end,
                    ]);
                }
                if (OzonOrdersCancellations::where('order_id', $iterator->current()['order_id'])->first() == null) {
                    OzonOrdersCancellations::create([
                        "cancel_reason_id" => $iterator->current()['cancellation']['cancel_reason_id'],
                        "cancel_reason" => $iterator->current()['cancellation']['cancel_reason'],
                        "order_id" => $iterator->current()['order_id'],
                        "cancellation_type" => $iterator->current()['cancellation']['cancellation_type'],
                        "cancellation_initiator" => $iterator->current()['cancellation']['cancellation_initiator'],
                    ]);
                }
                if (OzonOrdersDeliveryMethod::where('order_id', $iterator->current()['order_id'])->first() == null) {
                    OzonOrdersDeliveryMethod::create([
                        "delivery_method_id" => $iterator->current()['delivery_method']['id'],
                        "warehouse_id" => $iterator->current()['delivery_method']['warehouse_id'],
                        "order_id" => $iterator->current()['order_id'],
                        "tpl_provider_id" => $iterator->current()['delivery_method']['tpl_provider_id'],
                        "name" => $iterator->current()['delivery_method']['name'],
                        "warehouse" => $iterator->current()['delivery_method']['warehouse'],
                        "tpl_provider" => $iterator->current()['delivery_method']['tpl_provider'],
                    ]);
                }
                $iterator->next();
            }
            $i += 20;

        } while ($i < 1500);
        $memory = memory_get_usage();
        Log::channel('daily')->info("Данные по озоно записались в бд, выделилось памяти - ".$memory);
        return 0;
    }
}
