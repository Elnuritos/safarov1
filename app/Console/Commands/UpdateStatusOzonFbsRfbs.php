<?php

namespace App\Console\Commands;

use App\Models\OzonOrders;
use App\Models\InsalesProducts;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateStatusOzonFbsRfbs extends Command
{
    const OZON_STATUSES = [
        0 =>  'awaiting_registration',
        1 =>  'acceptance_in_progress',
        2 =>  'awaiting_approve',
        3 =>  'awaiting_packaging',
        4 =>  'awaiting_deliver',
        5 =>  'arbitration',
        6 =>  'client_arbitration',
        7 =>  'delivering',
        8 =>  'driver_pickup',
        9 =>  'not_accepted',
        10 =>  'sent_by_seller',
    ];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upd:fbs_rfbs_status';

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
        foreach (self::OZON_STATUSES as $key => $value) {

            $arr= OzonOrders::where('status', $value)
           ->get();
           foreach ($arr as $key => $val) {
           $posting_number=$val->posting_number;
          $headers = [
            "Client-Id" => "9111",
            "Api-Key" => "c4a1808a-c275-469f-9ef6-2e2bb7730d90",
        ];

            $url="https://api-seller.ozon.ru/v3/posting/fbs/get";
            $jayParsedAry = [
                "posting_number" => $posting_number,
                "translit" => true,
                "with" => [
                    "analytics_data" => true,
                    "financial_data" => true
                ]
            ];
            $res=Http::withHeaders($headers)->post($url,$jayParsedAry)->json();
            OzonOrders::where('posting_number',$posting_number)
            ->update(['status' => $res['result']['status']]);


          }
        }
    }
}
