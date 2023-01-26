<?php

namespace  App\Libraries;


use Illuminate\Support\Facades\Http;


class Pyrus
{
    public array $vars;
    protected $signature = 'test:start';
    public function __construct()
    {
        $this->vars = [
            'Authorization' => 'Bearer ' . $this->auth(),
            'Content-Type' => ' application/json;charset=UTF-8'
        ];
    }

    public function auth(): string
    {
        $url = getenv('PYRUS_API_URL') . 'auth';
        $response = Http::post($url, [
            'login' => getenv('PYRUS_BOT_LOGIN'),
            'security_key' => getenv('PYRUS_BOT_TOKEN')
        ])->json();

        return $response['access_token'];
    }

    public function getCatalog($catalog_id)
    {
        //140388
        $url = getenv('PYRUS_API_URL') . "catalogs/" . $catalog_id;
        return Http::withHeaders($this->vars)->get($url)->json();
    }

    public function getTaskId($task_id)
    {

        $task = $this->getTask($task_id);

        return $task['task']['id'];
    }

    public function getTaskFields($task_id)
    {

        $task = $this->getTask($task_id);

        return $task['task']['fields'];
    }
    public function getTask($task_id): array
    {

        return Http::withHeaders($this->vars)->get(getenv('PYRUS_API_URL') . "tasks/{$task_id}")->json();
    }
    public function getForm($form_id): array
    {

        return Http::withHeaders($this->vars)->get(getenv('PYRUS_API_URL') . "forms/{$form_id}")->json();
    }
    public function createTask($data): array
    {
        return Http::withHeaders($this->vars)->post(getenv('PYRUS_API_URL') . "tasks", $data)->json();
    }
    public function getFormId($form_id): array
    {

        $form = $this->getForm($form_id);

        return $form['form']['fields'];
    }

    public function getFile($file_id)
    {
        $url = getenv('PYRUS_API_URL') . 'files/download/' . $file_id;

        return Http::withHeaders($this->vars)->get($url)->body();
    }

    public function uploadFile($file_name, $file_path)
    {
        $header = $this->auth();

       /*  $file = "{$file_name}=@{$file_path}";
        $url = getenv('PYRUS_API_URL') . "files/upload";
        $test = md5($file_path);
        $headers=[
            'Content-Type'=>'application/pdf',
            'Authorization' => 'Bearer ' . $this->auth(),
        ];
        $res = Http::withHeaders($this->vars)->post($url, $test)->json(); */
        /* $q="curl -X POST
        https://api.pyrus.com/v4/files/upload
        -H 'Authorization: Bearer {$header}'
        -F 'test.pdf={$file_name}'"; */

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.pyrus.com/v4/files/upload");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['file_contents'=> curl_file_create($file_name)]);
        curl_setopt($ch, CURLOPT_HTTPHEADER,  array("Authorization: {$this->vars['Authorization']}",  "accept:application/pdf", "Content-type: multipart/form-data"));
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true)['guid'];
    }

    //END OF PYRUS



    public function addDefaultData($data)
    {

        $result = Http::post("http://195.161.69.22:4444/api/get-retail-default-choices", ['order_fields' => $data['fields']])->json();

        return $result;
    }

    public function addComment($task_id, $data)
    {

        return Http::withHeaders($this->vars)
            ->post(getenv('PYRUS_API_URL') . "tasks/{$task_id}/comments", $data)->json();
    }

    public function getFormTasks($form_id, $criteria)
    {
        return Http::withHeaders($this->vars)
            ->get(getenv('PYRUS_API_URL') . "forms/{$form_id}/register?" . http_build_query($criteria))
            ->json();
    }
    public function getFormTask($form_id)
    {
        return Http::withHeaders($this->vars)
            ->get(getenv('PYRUS_API_URL') . "forms/{$form_id}/register")
            ->json();
    }
}
