<?php
namespace App\Services\Actions;

use App\Models\InsalesProducts;
use Illuminate\Support\Facades\Http;
use Insales\Library\Insales as ApiInsales;

class RwInLastoverCount implements ActionInterface
{
    public $arr;
    public $article;

    public function __construct($arr)
    {
        $this->arr = $arr;

    }
    public function run()
    {

        $counter=intdiv(count($this->arr),500);

      /*   foreach ($this->arr as $key => $value) {
            if ($value['article']=="3014-89") {
                dd($value['count']);
            }
        }
        dd('s'); */
      for ($j=0; $j <$counter ; $j++) {
        $jayParsedAry = [
            "variants" => [

            ],
        ];
        for ($i=500*$j; $i <500*($j+1) ; $i++) {

            $variant_id=InsalesProducts::where('article', $this->arr[$i]['article'])->value('variant_id');
            $arik=[
                "id" =>$variant_id,
                "quantity" => $this->arr[$i]['count'],
            ];
            array_push($jayParsedAry['variants'], $arik);
        }
        $url1 = "https://323f9baffa77dfd407d9ee42731e3d7a:14aab7f0d7a89a0c0aa52dd4d54fc29c@dimaestri.com/admin/products/variants_group_update.json";
        $te = Http::put($url1, $jayParsedAry)->json();
      }

      if ($counter*500!=count($this->arr)) {
        $jayParsedAry = [
            "variants" => [

            ],
        ];
        for ($k=500*$counter; $k <count($this->arr) ; $k++) {

            $variant_id=InsalesProducts::where('article', $this->arr[$k]['article'])->value('variant_id');
            $arik=[
                "id" =>$variant_id,
                "quantity" => $this->arr[$k]['count'],
            ];
            array_push($jayParsedAry['variants'], $arik);
        }
        $te = Http::put($url1, $jayParsedAry)->json();
      }



    }

}
