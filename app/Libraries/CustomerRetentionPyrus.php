<?php

namespace  App\Libraries;


use Illuminate\Support\Facades\Http;
use GuzzleHttp\HandlerStack;

class CustomerRetentionPyrus
{


    public function getFileId($task_fields)
    {

        foreach ($task_fields as $key => $element) {
            if ($element['type'] == 'file') {
                foreach ($element as $key => $value) {
                    if ($key == 'value') {
                        foreach ($value as $key => $val) {
                            $id = $val['id'];
                        }
                    }
                }
            }
        }

        return $id;
    }

    public function findCheckmarkValue($task_fields)
    {

        foreach ($task_fields as $key => $element) {
            if ($element['type'] == 'checkmark') {
                foreach ($element as $key => $value) {
                    if ($key == 'value') {
                        $checkmark_value = $value;
                    }
                }
            }
        }

        return $checkmark_value;
    }
    public function findCheckmarkId($task_fields)
    {

        foreach ($task_fields as $key => $element) {
            if ($element['type'] == 'checkmark') {
                foreach ($element as $key => $value) {
                    if ($key == 'id') {
                        $checkmark_id = $value;
                    }
                }
            }
        }

        return $checkmark_id;
    }


    public function getFileName($task_fields)
    {

        foreach ($task_fields as $key => $element) {
            if ($element['type'] == 'file') {
                foreach ($element as $key => $value) {
                    if ($key == 'value') {
                        foreach ($value as $key => $val) {
                            $name = $val['name'];
                        }
                    }
                }
            }
        }

        return $name;
    }
}
