<?php

namespace App\Libraries;

use App\Models\Insales;
use App\Models\InsalesProducts;
use App\Models\InsalesUpdateOrders;
use Insales\Library\Insales as ApiInsales;

class Scv
{
    public function load()
    {

        $file    = storage_path("pyrus_tasks_sites_orders.csv");
        $csvFile = fopen($file, 'r');
        fgetcsv($csvFile);
        while (($getData = fgetcsv($csvFile, 10000, ",")) !== FALSE) {
            $name = $getData[0];
            $line = explode(";", $name);
            if (strlen($line[1]) > 5) {
                if (Insales::where('task_id', $line[0])->first() == null) {
                    Insales::create([
                        "task_id" =>  $line[0],
                        "order_id" =>  $line[1]
                    ]);
                }
            }
        }
    }
}
