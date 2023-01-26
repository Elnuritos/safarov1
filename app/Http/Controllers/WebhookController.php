<?php

namespace App\Http\Controllers;


use DOMDocument;
use XSLTProcessor;
use PHPPdf\Autoloader;
use PHPPdf\Core\Facade;
use App\Libraries\Pyrus;
use App\Libraries\WbSku;
use App\Libraries\QrAuth;
use App\Libraries\QrCreate;
use App\Libraries\QrStatus;
use App\Libraries\SberForm;
use Reworker\Factory as Ha;
use Illuminate\Http\Request;
use App\Libraries\SberComment;
use App\Libraries\InsalesPyrus;
use App\Libraries\SberCallback;
use App\Models\InsalesProducts;
use App\Jobs\InsalesUpdateBonus;
use App\Libraries\PyrusSberForm;
use App\Services\Diadok\ApiCalls;
use Ozon\Library\Ozon as ApiOzon;
use App\Models\InsalesBonusSystem;
use App\Libraries\ReworkerWriteOff;
use App\Http\Controllers\Controller;
use App\Libraries\CustomerRetention;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use PHPPdf\Core\Configuration\Loader;
use App\Libraries\InsalesSberCallback;
use App\Services\Actions\InsalesBonus;
use Insales\Library\Insales as ApiInsales;

class WebhookController extends Controller
{
    public function sendJson(Request $request)
    {
        $var = $request->all();

        if ($var['task']['form_id'] == 1118816) {

            $task_id = $var['task']['id'];
            return $this->getJson($task_id);
        }
        if ($var['task']['form_id'] == 1046999) {

            $task_id = $var['task']['id'];
            return $this->Comment($task_id);
        }
        if ($var['task']['form_id'] == 1138320) {

            $task_id = $var['task']['id'];
            return $this->SetReworker($task_id);
        }
    }
    public function getInsalesBonus(Request $request){
        dd($request);
        $arr = $request->all();
        $action = new InsalesBonus($arr);
        InsalesUpdateBonus::dispatch($action);
    }
    //Parser

    public function getJson($task_id)
    {
        return (new CustomerRetention())->getJson($task_id);
    }

    //SBER

    public function Callback()
    {
        return (new SberCallback())->getCallback();
    }
    public function getFrom($task_id)
    {
        return (new SberForm())->getForm($task_id);
    }
    public function Pay($task_id)
    {
        return (new SberForm())->Pay($task_id);
    }

    public function Comment($task_id)
    {
        return (new SberComment())->getComment($task_id);
    }
    public function CallbackInsales(Request $request)
    {
        return (new InsalesSberCallback())->getCallback($request);

    }
    public function filterCategoryAkt(){
        $boxId="91f8dbfc1866435a8674543f7d5d8445@diadoc.ru";
        $test=new ApiCalls();
        $filterCategoryAkt= "AcceptanceCertificate.Inbound";
        $res=$test->GetDocuments($boxId,$filterCategoryAkt);
        foreach ($res['Documents'] as $key => $value) {

            $entityId=$value['EntityId'];
            $messageId=$value['MessageId'];
            $content=$test->GetEntityContent($boxId,$messageId,$entityId);

           // $html_utf8 = mb_convert_encoding($content, "utf-8");


           // file_put_contents(storage_path('/test.pdf'),$html_utf8);
         //   header('Content-Type: application/pdf');


        file_put_contents(storage_path('/test.pdf'), "");
        file_put_contents(storage_path('/test.pdf'),$content);
        $te=(new Pyrus)->uploadFile(storage_path('/test.pdf'),"test.pdf");
            $data=[
                "attachments"=>[
                    [
                        "guid"=>$te,
                    ]
                ]
                    ];
                $res=(new Pyrus)->addComment("143676824",$data);


        }

    }
    public function tre($task_id)
    {

        $url = "https://323f9baffa77dfd407d9ee42731e3d7a:14aab7f0d7a89a0c0aa52dd4d54fc29c@dimaestri";
        $client = new ApiInsales($url);
        $product=$client->getOrderById("67016386")->getData();
        dd($product);


        $boxId="91f8dbfc1866435a8674543f7d5d8445@diadoc.ru";
        $test=new ApiCalls();
        /* $v=$test->d();
         $html_utf8 = mb_convert_encoding($v, "utf-8","windows-1251");
           header('Content-Type: application/xls');
           print_r($html_utf8);
        dd('s');
        $a=$test->GetDocumentTypes($boxId);
        dd($a['DocumentTypes'][10]); */
        //$res=$test->GetMyOrganizations();
      //  $res=$test->GetLastEvent($boxId);
        $filterCategoryChetFactura="Invoice.InboundFinished";
        $filterCategoryXmlTorg="XmlTorg12.InboundNotFinished";
        $filterCategoryTorg="Torg13.InboundNotFinished";

        $filterCategoryXmlAkt2="XmlAcceptanceCertificate.InboundNotFinished";
        $filterCategoryAkt2="AcceptanceCertificate.InboundNotFinished";
        $upd="UniversalTransferDocument.InboundNotFinished";
        $res=$test->GetDocuments($boxId,$filterCategoryChetFactura);

        $i=0;

        foreach ($res['Documents'] as $key => $value) {
            if($key==98){
            $entityId=$value['EntityId'];
            $messageId=$value['MessageId'];
            $content=$test->GetEntityContent($boxId,$messageId,$entityId);

            $html_utf8 = mb_convert_encoding($content, "utf-8","windows-1251");


           file_put_contents(storage_path('/test.xml'), "");
           file_put_contents(storage_path('/test.xml'),$content);
           $xml=simplexml_load_string($content);

     //     print_r($xml);
   //   dd($xml,(string)$xml->СвУчДокОбор['ИдОтпр']);
           $title="Счет-фактура №".(string)$xml->Документ->СвСчФакт['НомерСчФ']." от ".(string)$xml->Документ->СвСчФакт['ДатаСчФ'];
           $seller=(string)$xml->Документ['НаимЭконСубСост'];
         //  dd($title);
           $address="Россия,".(string)$xml->Документ->СвСчФакт->СвПрод->Адрес->АдрИнф['АдрТекст'];
           $inn_kpp=(string)$xml->Документ->СвСчФакт->СвПрод->ИдСв->СвЮЛУч['ИННЮЛ']."/".(string)$xml->Документ->СвСчФакт->СвПрод->ИдСв->СвЮЛУч['КПП'];
           $number_pay_doc=(string)$xml->Документ->СвСчФакт->СвПРД['НомерПРД'];
           $date_pay_doc=(string)$xml->Документ->СвСчФакт->СвПРД['ДатаПРД'];
           $shipment_num=(string)$xml->Документ->СвСчФакт['НомерСчФ'];
           $shipment_date=(string)$xml->Документ->СвСчФакт['ДатаСчФ'];
           $buyer=(string)$xml->Документ->СвСчФакт->СвПокуп->ИдСв->СвЮЛУч['НаимОрг'];
           $address_buyer="Россия,".(string)$xml->Документ->СвСчФакт->СвПокуп->Адрес->АдрИнф['АдрТекст'];
           $inn_kpp_buyer=(string)$xml->Документ->СвСчФакт->СвПокуп->ИдСв->СвЮЛУч['ИННЮЛ']."/".(string)$xml->Документ->СвСчФакт->СвПокуп->ИдСв->СвЮЛУч['КПП'];
           $nds=(string)$xml->Документ->ТаблСчФакт->ВсегоОпл->СумНалВсего->СумНал;

            $res=[];
            $i=0;
           foreach ($xml->Документ->ТаблСчФакт->СведТов as $key => $value) {

            $array_push=[
                'row_id' => $i,
                            'cells' => [
                                [
                                    'id' => 47,
                                    'value' => (string)$value['НомСтр'],
                                ],
                                [
                                    'id' => 48,
                                    'value' => (string)$value['НаимТов'],
                                ],
                                [
                                    'id' => 54,
                                    'value' => (string)$value['СтТовБезНДС'],
                                ],
                                [
                                    'id' => 55,
                                    'value' => (string)$value->Акциз->БезАкциз ,
                                ],
                                [
                                    'id' => 56,
                                    'value' => (string)$value['НалСт'],
                                ],
                                [
                                    'id' => 57,
                                    'value' => (string)$value->СумНал->СумНал,
                                ],
                                  [
                                    'id' => 58,
                                    'value' => (string)$value['СтТовУчНал'],
                                ],

                            ]];
                            $i+=1;
                            array_push($res,$array_push);




           }


           $sum_nonds=(string)$xml->Документ->ТаблСчФакт->ВсегоОпл['СтТовБезНДСВсего'];
           $sum_nds=(string)$xml->Документ->ТаблСчФакт->ВсегоОпл['СтТовУчНалВсего'];
           $fio=(string)$xml->Документ->Подписант->ЮЛ->ФИО['Фамилия']." ".(string)$xml->Документ->Подписант->ЮЛ->ФИО['Имя']." ".(string)$xml->Документ->Подписант->ЮЛ->ФИО['Отчество'];
           $data=[
            "form_id" => 1193109,
                        "fields" => [
                            [
                                "id" => 5,
                                //"id" => 3,
                                "value" => $title,
                            ],
                            [
                                "id" => 28,
                                //"id" => 2,
                                "value" => $seller,
                            ],
                            [
                                "id" => 12,
                                //"id" => 2,
                                "value" => $address,
                            ], [
                                "id" => 36,
                                //"id" => 2,
                                "value" => $inn_kpp,
                            ], [
                                "id" => 39,
                                //"id" => 2,
                                "value" => $number_pay_doc,
                            ], [
                                "id" => 65,
                                //"id" => 2,
                                "value" => $date_pay_doc,
                            ], [
                                "id" => 40,
                                //"id" => 2,
                                "value" => $shipment_num,
                            ], [
                                "id" => 66,
                                //"id" => 2,
                                "value" => $shipment_date,
                            ],
                            [
                                "id" => 41,
                                //"id" => 2,
                                "value" => $buyer,
                            ],
                            [
                                "id" => 42,
                                //"id" => 2,
                                "value" => $address_buyer,
                            ],
                            [
                                "id" => 43,
                                //"id" => 2,
                                "value" => $inn_kpp_buyer,
                            ],
                            [
                                "id" => 69,
                                //"id" => 2,
                                "value" => $sum_nonds,
                            ],
                            [
                                "id" => 71,
                                //"id" => 2,
                                "value" => $sum_nds,
                            ],
                            [
                                "id" => 46,
                                //"id" => 2,
                                "value" => $res,
                            ],
                            [
                                "id" => 67,
                                //"id" => 2,
                                "value" => $fio,
                            ],
                            [
                                "id" => 70,
                                //"id" => 2,
                                "value" => $nds,
                            ],
                            ]

           ];

           $te=(new Pyrus)->createTask($data);

           dd($te);







           $te=(new Pyrus)->uploadFile(storage_path('/test.xml'),"test.xml");
           $data=[
               "attachments"=>[
                   [
                       "guid"=>$te,
                   ]
               ]
                   ];
               $res=(new Pyrus)->addComment("143676824",$data);
                   $i+=1;
                   if ($i==2) {
                       dd('s');
                   }
                   dd('s');














            $nodeJsPath=storage_path('/test.js');
            $ret = exec("node ".$nodeJsPath.' 2>&1', $out, $err);
            dd($out);
            dd($content);
         //   header('Content-Type: application/pdf');






                }
        }


           /*  $asd=base64_decode($content);
            dd($asd); */

         //   $d=simplexml_load_string($html_utf8);

            dd('s');








        $url="https://order-admin.dimaestri-serve.ru/api/reworker/products";
        $result=Http::post($url)->json();
        dd($result['products'][4]);
        $variant_id=426947788;
        $product_id=250258223;
        $url = "https://323f9baffa77dfd407d9ee42731e3d7a:14aab7f0d7a89a0c0aa52dd4d54fc29c@dimaestri.com";
        $client = new ApiInsales($url);




        $jayParsedAry = [
          "variants" => [
                [
                   "id" => $variant_id,
                   "quantity" => 42
                ]
             ]
       ];




         $url1="https://323f9baffa77dfd407d9ee42731e3d7a:14aab7f0d7a89a0c0aa52dd4d54fc29c@dimaestri.com/admin/products/variants_group_update.json";
         $te=Http::put($url1,$jayParsedAry)->json();
         dd($te,'d');
       //$product = $client->getProductById('250258174')->getResponse();
        $upd_pr = $client->getProductById(250258223)->getResponse();
        dd($upd_pr);
        //dd($product['data']['variants'][0]['id']);

        $order_update = $client->getProducts(['per_page' => 250, "page" => 1])->getResponse();
        foreach ($order_update['data'] as $key => $val) {
            foreach ($val as $key => $value) {
                // dd($val);
                if ($key == "variants") {
                    if ($value[0]['available'] != false) {
                        dd($val);
                    }
                }
            }

        }
        dd($order_update['data']);
        $pyrus = new Pyrus();

        $json = $pyrus->getForm(1046999);
        dd($json);
        $response = [
            'response' => ", 11",
        ];
        $f1 = explode(", ", $response['response']);
        dd(empty($f1[1]));
        $response = Http::post(getenv('CRM_API_URL'), [
            'action' => 'Site/createOrderTaskLink',
            'task_id' => "145630443",
            'order_id' => "65451228",
        ])->json();
        dd($response);
        $d = "11.00000000000";
        dd((int) $d);
        $url = "https://sberbank.pimentos.net/payment/callback?mdOrder=39a824f9-ab66-7e70-89a9-4ff1026661d4";
        $res = Http::get($url);

        $res = (new InsalesPyrus)->getInsales();
        dd($res);
        /* $task_id="144984871";

        $order_id="65243789";
        $res= Http::post(getenv('CRM_API_URL'), [
        'action' => 'Site/createOrderTaskLink',
        'task_id' => $task_id,
        'order_id' => $order_id
        ]);
        dd($res);
        return $res; */
/* $pyrus = new Pyrus();

$json = $pyrus->getForm(1046999);
dd($json);
$task_list = (new Pyrus())->getFormTasks(1046999, [
'fld201' => 19,
'created_after' => date("Y-m-d", strtotime('-2 week')) . 'T10:00:00Z',

]);
dd($task_list); */
        //  $p = $pyrus->getTaskFields($task_id);
        /*         $json = (new Insale())->getOrders();
        dd($json);
        $task_list = (new Pyrus())->getFormTasks(1046999, [
        'fld49' =>  1,
        'created_after' => date("Y-m-d", strtotime('-1 week')) . 'T10:00:00Z',

        ]);
        $arrayobject = \SplFixedArray::fromArray($task_list['tasks']);

        $iterator = $arrayobject->getIterator();
        while ($iterator->valid()) {
        $task_id = $iterator->current()['id'];
        $query = "curl -X POST -d 'action=Insales/getOrderIDByTaskID' -d 'task_id={$task_id}' https://crm2.squesito.ru/Public/Api2/index.php";
        exec($query, $out);
        $res = json_decode($out[0], true);
        $order_id = $res['result'];

        $flight = Insales::firstOrCreate([
        'order_id' => $order_id,
        'task_id' => $task_id,
        ]);
        dd(Insales::all());
        if ($order_id != '0') {
        # code...
        }

        $iterator->next();
        }
        return $json; */
        /*
        $task = (new Pyrus())->createTask(1124748);
        $task_id=$task['task']['id']; */
        /*         Insales::truncate();
    dd(Insales::all()); */
    }

    //Mpstats
    public function getExcel($task_id)
    {
        return (new WbSku())->makeFile($task_id);
    }
    public function Upload()
    {
        $pyrus = new Pyrus();
        $create_list = 'TEST703.xlsx';
        $file_path = storage_path("/{$create_list}");
        $k = $pyrus->uploadFile($create_list, $file_path);
        dd($k);
    }

    public function qr($task_id)
    {
        $scope = getenv('QR_SBP_CREATE');
        $tokens = (new QrAuth())->auth($scope);

        $res = (new QrCreate())->create($task_id, $tokens);

        dd($res);
        $scope_status = getenv('QR_SBP_STATUS');
        $status_token = (new QrAuth())->auth($scope_status);
        $res_status = (new QrStatus())->status($task_id, $status_token, $res['order_id']);

        return $res_status;
    }

    //Reworker write-off

    public function SetReworker($task_id)
    {

        return (new ReworkerWriteOff)->WriteOff($task_id);
    }
    public function testapi()
    {
        $url = ['base_uri' => "http://195.161.69.22:4444/api/reworker-products/articles/"];

        $testrw = new Ha();

        $rw = $testrw->init(getenv('ORDER_ADMIN_USER'), getenv('ORDER_ADMIN_PASS'));
        $cat_treit = (new Pyrus)->getCatalog(149516);
        $items = $rw->orders->getOrderProducts(26078297);

        $rw_order = [];

        $arr_rw = \SplFixedArray::fromArray($items);
        $iterator_rw = $arr_rw->getIterator();
        $word = "ТРЕЙБОКС";
        $treit = \SplFixedArray::fromArray($cat_treit['items']);
        $iterator_treit = $treit->getIterator();
        while ($iterator_rw->valid()) {
            $arr = [
                'article' => '',
                'price' => '',
                'quantity' => '',
            ];
            if (strpos($iterator_rw->current()['productOfferRaw']['name'], $word) !== false) {
                while ($iterator_treit->valid()) {
                    if ($iterator_rw->current()['productOfferRaw']['article'] == $iterator_treit->current()['values'][0]) {
                        $arr = [
                            'article' => $iterator_treit->current()['values'][2],
                            'price' => $iterator_rw->current()['price'] / ($iterator_treit->current()['values'][4] * $iterator_rw->current()['count']),
                            'quantity' => $iterator_treit->current()['values'][4] * $iterator_rw->current()['count'],
                        ];
                    }

                    $iterator_treit->next();
                }
            } else {

                $arr = [
                    'article' => $iterator_rw->current()['productOfferRaw']['article'],
                    'price' => $iterator_rw->current()['price'],
                    'quantity' => $iterator_rw->current()['count'],
                ];
            }

            array_push($rw_order, $arr);
            $iterator_rw->next();
        }
        $url = "https://323f9baffa77dfd407d9ee42731e3d7a:14aab7f0d7a89a0c0aa52dd4d54fc29c@dimaestri.com";
        $client = new ApiInsales($url);
        $order = $client->getOrders(['per_page' => 1, "page" => 1])->getResponse();

        $arrayobject = \SplFixedArray::fromArray($order['data']);
        $iterator = $arrayobject->getIterator();
        while ($iterator->valid()) {
            $order_id = strval($iterator->current()['id']);
            $res_table = [];
            $res_client = [];
            $order_line_id = [];
            $order_lines = \SplFixedArray::fromArray($iterator->current()['order_lines']);
            $iterator1 = $order_lines->getIterator();
            while ($iterator1->valid()) {
                $table_info = [
                    'article' => '',
                    'price' => '',
                    'quantity' => '',

                ];
                $ids = [
                    "id" => '',
                ];
                $ids['id'] = $iterator1->current()['id'];
                $table_info['price'] = $iterator1->current()['sale_price'];
                $table_info['quantity'] = $iterator1->current()['quantity'];
                $table_info['article'] = $iterator1->current()['sku'];
                array_push($res_table, $table_info);
                array_push($order_line_id, $ids);
                $iterator1->next();
            }
            $iterator->next();
        }
        //dd($rw_order,$res_table);
        $tik = array_merge($rw_order, $res_table);
        //  dd($tik);
        $collection = collect($tik);
        $unique = $collection->unique();
        $unique = $collection->unique(function ($bb) {
            return $bb['article'] . $bb['quantity'] . $bb['price'];
        });

        $counter = 0;
        $insales_update = [];
        foreach ($unique as $key => $value) {
            $res_order = [

                "order_lines_attributes" => [
                    [
                        "id" => 1,
                        "title" => "",
                        "sale_price" => 100,
                        "quantity" => 2,

                    ],
                ],

            ];

            $res_order['order_lines_attributes']['id'] = $order_line_id[$counter]['id'];
            $res_order['order_lines_attributes']['sale_price'] = $value['price'];
            $res_order['order_lines_attributes']['quantity'] = $value['quantity'];
            $res_order['order_lines_attributes']['title'] = InsalesProducts::where('article', $value['article'])->value('title');
            array_push($insales_update, $res_order);
            //   $unique[0]->put('title',$title);
            $counter += 1;
        }
        dd($insales_update);

        if ($unique->values()->all() != $res_table) {
            //update
            /*   $order_update=[
        "order"
        ] */
        } else {
            //non update

        }
        dd($unique->values()->all());
        dd($rw);
    }
    public function removeupdate()
    {
    }
}
