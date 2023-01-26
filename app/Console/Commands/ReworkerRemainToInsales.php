<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ReworkerInsalesRemain;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use App\Services\Actions\RwInLastoverCount;

class ReworkerRemainToInsales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:insales_reworker_remain';

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
        $test=Redis::get('reworker_products');
        $result = json_decode($test, JSON_UNESCAPED_UNICODE);
        $i = 0;

        $res = \SplFixedArray::fromArray($result['products']);
        $iterator_res = $res->getIterator();
        $arr=[];
        while ($iterator_res->valid()) {
            $i+=1;
            $irr = 0;
            if ($iterator_res->current()['items'] != null) {
                foreach ($iterator_res->current()['items'] as $key => $value) {
                    if ($value['state'] == "normal") {
                        $irr += 1;
                        $article = $iterator_res->current()['article'];
                        $count = $value['count'];
                        $push=[
                            "count"=>$count,
                            "article"=>$article
                        ];
                        array_push($arr,$push);
                    }

                }

            }
            if ($irr == 0) {

                $article = $iterator_res->current()['article'];
                $count = 0;
                $push=[
                    "count"=>$count,
                    "article"=>$article
                ];
                array_push($arr,$push);
            }
            $iterator_res->next();
        }

        $action = new RwInLastoverCount($arr);
        ReworkerInsalesRemain::dispatch($action);
    }
}
