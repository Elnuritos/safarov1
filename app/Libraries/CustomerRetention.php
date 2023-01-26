<?php

namespace  App\Libraries;


use App\Libraries\Pyrus;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Http;
use App\Libraries\CustomerRetentionPyrus;
use App\Libraries\Parser;

class CustomerRetention
{
    public function getJson($task_id)
    {
        $pyrus = new Pyrus();
        $pyrusRetention = new CustomerRetentionPyrus();
        $task_fields = $pyrus->getTaskFields($task_id);
        $checkmark_value = $pyrusRetention->findCheckmarkValue($task_fields);
        $checkmark_id = $pyrusRetention->findCheckmarkId($task_fields);

        if ($checkmark_value  == 'checked') {
            return 'already checked';
        } else {
            $checkmark_value  = 'checked';
        }
        $file_id = $pyrusRetention->getFileId($task_fields);
        $file_name = $pyrusRetention->getFileName($task_fields);
        $file = $pyrus->getFile($file_id);

        if (isset($file)) {
            file_put_contents(storage_path('/' . $file_name), $file);
        }

        $parser = new Parser();
        $items = $parser->parse($task_id);
        $finale = json_encode($items, JSON_UNESCAPED_UNICODE);
        $decoded = json_decode($finale, true);
        $response = Http::post(getenv('CRM_API_URL'), $decoded)->json();
        $qnty = count($response['queries']);

        if (empty($response['errors'])) {
            $text = "Обновлено-" . $qnty . " клиентов.";
            $data = [
                'formatted_text' => $text,
                'field_updates' => [[
                    "id" => $checkmark_id,
                    "value" => $checkmark_value
                ]],
                'action' => 'finished'
            ];
        } else {
            $text = "Обновлено-" . $qnty . " клиентов. Получены ошибки:<br>" . implode("<br>", $response['errors']) . "";
            $data = [
                'formatted_text' => $text,
            ];
        }
        $res_bot = json_encode($data, JSON_UNESCAPED_UNICODE);

        $check = $pyrus->addComment($task_id, $data);

        return $res_bot;
    }
}
