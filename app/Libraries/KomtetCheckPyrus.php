<?php

namespace  App\Libraries;


use Illuminate\Support\Facades\Http;
use GuzzleHttp\HandlerStack;

class KomtetCheckPyrus
{
    public function getVal($task_id)
    {
        $pyrusSber = new PyrusSberForm();
        $pyrus = new Pyrus();
        $task_fields = $pyrus->getTaskFields($task_id);
        $phone = $pyrusSber->getPhone($task_fields);
        $ship = $pyrusSber->getShipment($task_fields);
        $bonus = $pyrusSber->getBonus($task_fields);
        $money = $pyrusSber->getFieldValue($task_fields);
        $tovar = $pyrusSber->getTable($task_fields);
        $upd = $pyrusSber->getUpd($task_fields);
        if ($upd != 0) {
            $money += $upd;
        }
        $items = $this->getItems($tovar, $ship, $upd);
        $res = [
            'task_id' => $task_id,
            'phone' => $phone,
            'items' => $items,
            'discount' => $bonus,
        ];
        return $res;
    }
    public function getItems($tovar, $ship, $upd)
    {
        $result = [];
        $i = 0;
        foreach ($tovar as $key => $value) {
            $i += 1;
            $row_content = [
                "name" => '',
                "count" => '',
                "price" => '',
                "total" => '',
            ];
            foreach ($value as $key => $v) {
                if ($key == 'cells') {
                    foreach ($v as $key => $v1) {


                        if ($v1['name'] == 'Наименование') {
                            foreach ($v1 as $key => $v2) {
                                if ($key == 'value') {
                                    foreach ($v2 as $key => $v3) {
                                        if ($key == 'values') {
                                            foreach ($v3 as $key => $v4) {

                                                $row_content['name'] = $v3[3];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if ($v1['name'] == 'Цена') {

                            foreach ($v1 as $key => $v2) {

                                $row_content['price'] = $v1['value'];
                            }
                        }
                        if ($v1['name'] == 'Количество') {
                            foreach ($v1 as $key => $v2) {

                                $row_content['count'] = $v1['value'];
                            }
                        }
                        if ($v1['name'] == 'Сумма') {
                            foreach ($v1 as $key => $v2) {

                                $row_content['total'] = $v1['value'];
                            }
                        }
                    }
                }
            }

            array_push($result, $row_content);
        }
        if ($ship) {
            $row_content = [
                "name" => 'Доставка',
                "count" => 1,
                "price" => $ship,
                "total" => $ship,
            ];
            array_push($result, $row_content);
        }
        if ($upd) {
            $row_content = [
                "name" => 'Доставка УПД',
                "count" => 1,
                "price" => $upd,
                "total" => $upd,
            ];
            array_push($result, $row_content);
        }
        return $result;
    }
}
