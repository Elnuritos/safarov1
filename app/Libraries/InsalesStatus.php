<?php

namespace  App\Libraries;


use App\Models\Insales;
use App\Libraries\Pyrus;
use GuzzleHttp\HandlerStack;
use App\Models\ReworkerStatuses;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Http;
use Insales\Library\Insales as ApiInsales;

class InsalesStatus
{
    const ACTIVE_STATUSES = [
        "pending_queued" => "new",
        "confirmed" => "accepted",
        "assembling" => "approved",
        "delivery" => "dispatched",
        "complete" => "delivered",
    ];
    public function getStatus($kitForm, $codeStatus)
    {
        $task_id = $kitForm['task']['id'];
        $codeStatus = ReworkerStatuses::where('task_id',  $task_id)->value('order_status');
        $order_id = Insales::where("task_id", $task_id)->value('order_id');
        if ($order_id != null) {
            foreach (self::ACTIVE_STATUSES as $key => $value) {
                if ($key == $codeStatus) {
                    $data['order'] = [
                        "custom_status_permalink" => $value,
                    ];
                    try {
                        $url = "https://323f9baffa77dfd407d9ee42731e3d7a:14aab7f0d7a89a0c0aa52dd4d54fc29c@dimaestri.com";
                        $client = new ApiInsales($url);
                        $products_count = $client->UpdateCustomStatus($order_id, $data)->getResponse();

                        //    Log::channel('daily')->info("arr for new status step4:");
                    } catch (\Exception $e) {

                        Log::channel('daily')->warning("ошибка", ['msg' => $e]);
                    }
                }
            }



            //  dd($products_count, $data);
        }
    }
}
