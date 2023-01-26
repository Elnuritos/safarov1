<?php

namespace  App\Libraries;



use App\Libraries\CustomerRetentionPyrus;

class WbSku
{
    public function makeFile($task_id)
    {
        $pyrus = new Pyrus();

        $task_fields = $pyrus->getTaskFields($task_id);
        $file_id = (new CustomerRetentionPyrus())->getFileId($task_fields);
        $file_name = (new CustomerRetentionPyrus())->getFileName($task_fields);
        $file = $pyrus->getFile($file_id);

        if (isset($file)) {
            file_put_contents(storage_path('/' . $file_name), $file);
        }

        $parser = new Parser();
        $sku = $parser->parse_mp($task_id);

        // $link = $mp->getLink($sku);

        $wb = new Wb();
        $package_type_arr = [];
        $content_arr = [];
        $name_arr = [];
        $i = 0;

        foreach ($sku['ids'] as $key => $value) {
            if ($key > 2000 && $key < 3001) {


                $items = $wb->getItem($value);
                if (isset($items['options'])) {

                    $package_type = $wb->getPackageType($items);
                    if (!isset($package_type)) {
                        $package_type = 'пусто';
                    }
                } else {
                    $package_type = 'пусто?(обрати внимание)';
                }

                if (isset($items['contents'])) {
                    $content =  $items['contents'];
                } else {
                    $content = 'пусто';
                }
                if (isset($items['imt_name'])) {
                    $name =  $items['imt_name'];
                } else {
                    $name = 'пусто';
                }

                array_push($package_type_arr, $package_type);
                array_push($content_arr, $content);
                array_push($name_arr, $name);
                /* if ($i == 2000) {

            break;
        }
        $i += 1; */
            }
        }
        $ski = [];

        for ($i = 2001; $i < 3001; $i++) {
            array_push($ski, $sku['ids'][$i]);
        }

        $create_list = $parser->getList($package_type_arr, $content_arr, $ski, $name_arr);
        dd($create_list);
        $file_path = storage_path("/{$create_list}");
        $pyrus->uploadFile($create_list, $file_path);
    }
}
