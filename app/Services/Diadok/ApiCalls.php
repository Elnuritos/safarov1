<?php

namespace App\Services\Diadok;

use Illuminate\Support\Facades\Http;

class ApiCalls
{
    public array $vars;

    public function __construct()
    {
        $this->vars = [
            'Authorization' => "DiadocAuth ddauth_api_client_id=".getenv('DIADOK_API_KEY').",ddauth_token=".$this->auth(),
            'Content-Type' => ' application/json;charset=UTF-8'
        ];
    }
    public function auth()
    {
        $url = getenv('DIADOK_API_PATH') . 'V3/Authenticate?type=password';
        $headers=[
               "Authorization"=>"DiadocAuth ddauth_api_client_id=".getenv('DIADOK_API_KEY')
        ];
        $response = Http::withHeaders($headers)->post($url, [
            'login' => getenv('DIADOK_API_LOGIN'),
            'password' => getenv('DIADOK_API_PASSWORD'),
        ])->body();
       return $response;
    }
    public function GetMessage($boxId,$messageId)
    {

    }
    public function GetLastEvent($boxId)
    {
        $url = getenv('DIADOK_API_PATH') . '/GetLastEvent?boxId='.$boxId;
        $response = Http::withHeaders($this->vars)->get($url)->json();
        return $response;

    }
    public function GetMyOrganizations()
    {
        $arr=['autoRegister' => "false"];
        $url = getenv('DIADOK_API_PATH') . '/GetMyOrganizations';
        $response = Http::withHeaders($this->vars)->get($url, $arr)->json();
        return $response;
    }
    public function GetDocuments($boxId,$filterCategory)
    {
        $url = getenv('DIADOK_API_PATH') . '/V3/GetDocuments?boxId='.$boxId."&filterCategory=".$filterCategory;
        $response = Http::withHeaders($this->vars)->get($url)->json();
        return $response;

    }
    public function GetDocumentTypes($boxId)
    {
        $url = getenv('DIADOK_API_PATH') . '/V2/GetDocumentTypes?boxId='.$boxId;
        $response = Http::withHeaders($this->vars)->get($url)->json();
        return $response;

    }
    public function GetEntityContent($boxId,$messageId,$entityId)
    {
        $url = getenv('DIADOK_API_PATH') . '/V4/GetEntityContent?boxId='.$boxId."&messageId=".$messageId."&entityId=".$entityId;
        $response = Http::withHeaders($this->vars)->get($url)->body();
        return $response;
    }
    public function d()
    {
        $url = getenv('DIADOK_API_PATH') ."/GetContent?typeNamedId=XmlAcceptanceCertificate&function=default&version=utd820_05_01_02_hyphen&titleIndex=0&contentType=UserContractXsd";
        $response = Http::withHeaders($this->vars)->get($url)->body();
        return $response;
    }
}
