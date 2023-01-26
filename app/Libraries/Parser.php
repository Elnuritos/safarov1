<?php

namespace  App\Libraries;

use App\Libraries\Pyrus;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Libraries\CustomerRetentionPyrus;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Parser
{
    function recursive_change_key($arr, $set)
    {
        if (is_array($arr) && is_array($set)) {
            $newArr = array();
            foreach ($arr as $k => $v) {

                $key = array_key_exists($k, $set) ? $set[$k] : $k;

                $newArr[$key] =  is_array($v) ?  $this->recursive_change_key($v, $set)  : $v;
            }
            return $newArr;
        }
        return $arr;
    }
    public function parse($task_id)
    {
        $pyrus = new Pyrus();
        $pyrusRetention = new CustomerRetentionPyrus();
        $task_fields = $pyrus->getTaskFields($task_id);
        $file_name = $pyrusRetention->getFileName($task_fields);
        $file_extension = explode('.', $file_name)[1];
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader(ucfirst($file_extension));
        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load(storage_path('/' . $file_name));
        $worksheet = $spreadsheet->getActiveSheet();
        $results = [];

        foreach ($worksheet->getRowIterator() as $row) {

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE);

            $row_content = [];

            foreach ($cellIterator as $key => $cell) {
                array_push($row_content, $cell->getcalculatedValue());
            }
            $results[] = $row_content;
        }
        $header = array_shift($results);
        $res = collect($results)
            ->map(fn ($item) => [
                $header[0] => $item[0],
                $header[1] => $item[1]
            ])->all();

        $arr = array(
            'action' => 'Clients/fixClientsToManagers',
            'rows' => $res,
        );

        return $arr;
    }



    public function parse_mp($task_id)
    {
        ini_set('max_execution_time', 0);
        $pyrus = new Pyrus();
        $pyrusRetention = new CustomerRetentionPyrus();
        $task_fields = $pyrus->getTaskFields($task_id);
        $file_name = $pyrusRetention->getFileName($task_fields);
        $file_extension = explode('.', $file_name)[1];
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader(ucfirst($file_extension));
        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load(storage_path('/' . $file_name));
        $worksheet = $spreadsheet->getActiveSheet();
        $results = [];

        foreach ($worksheet->getRowIterator() as $row) {

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE);

            $row_content = [];

            foreach ($cellIterator as $key => $cell) {
                array_push($row_content, $cell->getcalculatedValue());
            }
            $results[] = $row_content;
        }

        $header = array_shift($results);
        //  dd($results);
        $arr = ['ids' => []];
        foreach ($results as $key => $v) {
            foreach ($v as $key => $v1) {
                array_push($arr['ids'], $v1);
            }
        }

        return $arr;
    }


    public function getList($package_type_arr, $content_arr, $ski, $name_arr)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Sku');
        $sheet->setCellValue('B1', 'Название');
        $sheet->setCellValue('C1', 'Ссылка');
        $sheet->setCellValue('D1', 'Количество капсул/пакетиков');
        $sheet->setCellValue('E1', 'Комплектация');
        $i = 2;
        foreach ($ski as $key => $value) {

            $sheet->setCellValue('A' . $i, $value);
            $sheet->setCellValue('C' . $i, "https://www.wildberries.ru/catalog/{$value}/detail.aspx");
            $i += 1;
        }
        $i = 2;
        foreach ($name_arr as $key => $value) {

            $sheet->setCellValue('B' . $i, $value);
            $i += 1;
        }
        $i = 2;
        foreach ($package_type_arr as $key => $value) {

            $sheet->setCellValue('D' . $i, $value);
            $i += 1;
        }
        $i = 2;
        foreach ($content_arr as $key => $value) {

            $sheet->setCellValue('E' . $i, $value);
            $i += 1;
        }
        $writer = new Xlsx($spreadsheet);
        $url = 'TESTs' . $i . '.xlsx';
        $writer->save($url);
        return $url;
    }
}




/* $json = array(
    'action' => 'Clients/fixClientsToManagers',
    "rows" => array(
        array(
        "Код Клиента" => "",
        "Закрепленный менеджер" => "",
    )
)
); */
