<?php
namespace App\Services\Actions;

use App\Models\InsalesProducts;
use App\Models\InsalesBonusSystem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Insales\Library\Insales as ApiInsales;

class InsalesBonus implements ActionInterface
{
    public $arr;
    public $article;

    public function __construct($arr)
    {
        $this->arr = $arr;

    }
    public function run()
    {
        $url = "https://323f9baffa77dfd407d9ee42731e3d7a:14aab7f0d7a89a0c0aa52dd4d54fc29c@dimaestri.com/admin/clients/".$this->arr['client_id']."/bonus_system_transactions.json";
        $jayParsedAry = [
            "bonus_system_transaction" => [
                  "bonus_points" => -100,
                  "description" => "compliment"
               ]
         ];
         try {
            $res=Http::retry(3, 100)->post($url,$jayParsedAry)->json();
            InsalesBonusSystem::create([
                "order_id"=>$this->arr['order_id'],
                "in_order_id"=>$this->arr['in_order_id'],
                "client_id"=>$this->arr['client_id'],
                "bonus"=>$this->arr['bonus'],
                "exported"=>1,
            ]);
         } catch (\Exception$e) {
            InsalesBonusSystem::create([
                "order_id"=>$this->arr['order_id'],
                "in_order_id"=>$this->arr['in_order_id'],
                "client_id"=>$this->arr['client_id'],
                "bonus"=>$this->arr['bonus'],
                "exported"=>0,
            ]);
            Log::channel('warning')->warning("Не удалось добавить бонус за заказ {$this->arr['order_id']}", ['msg' => $e]);
         }



        dd($res);
    }

}
