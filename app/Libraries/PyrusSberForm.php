<?php

namespace  App\Libraries;




class PyrusSberForm
{
    public function getShipment($task_fields)
    {
        foreach ($task_fields as $key => $element) {

            if ($element['id'] == 100) {
                foreach ($element as $key => $value) {
                    if ($key == 'value') {
                        foreach ($value as $key => $v) {
                            if ($key == 'fields') {
                                foreach ($v as $key => $end) {

                                    if ($end['id'] == 152) {
                                        if (isset($end['value'])) {

                                            $res = $end['value'];
                                        } else {
                                            $res = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $res;
    }
    public function getBonus($task_fields)
    {
        foreach ($task_fields as $key => $element) {

            if ($element['id'] == 100) {
                foreach ($element as $key => $value) {
                    if ($key == 'value') {
                        foreach ($value as $key => $v) {
                            if ($key == 'fields') {
                                foreach ($v as $key => $end) {

                                    if ($end['id'] == 151) {
                                        if (isset($end['value'])) {

                                            $res = $end['value'];
                                        } else {
                                            $res = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $res;
    }
    public function getPhone($task_fields)
    {
        foreach ($task_fields as $key => $element) {
            if ($element['id'] == 148) {

                foreach ($element as $key => $value) {
                    if ($key == 'value') {
                        foreach ($value as $key => $v) {
                            if ($key == 'fields') {
                                foreach ($v as $key => $end) {
                                    foreach ($end as $key => $e) {
                                        if ($e == 'Телефон') {

                                            //dd($e['value']);
                                            if (!isset($end['value'])) {

                                                $phone = NULL;
                                            } else {
                                                $phone = $end['value'];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $phone;
    }

    public function getMail($task_fields)
    {
        foreach ($task_fields as $key => $element) {
            if ($element['id'] == 148) {
                foreach ($element as $key => $value) {
                    if ($key == 'value') {
                        foreach ($value as $key => $v) {
                            if ($key == 'fields') {
                                foreach ($v as $key => $end) {
                                    foreach ($end as $key => $e) {
                                        if ($e == 'Эл. почта') {
                                            if (!isset($end['value'])) {

                                                $mail = NULL;
                                            } else {
                                                $mail = $end['value'];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $mail;
    }
    public function getTable($task_fields)
    {
        $rows=[];
        foreach ($task_fields as $key => $element) {
            if ($element['name'] == 'Состав заказа') {
                foreach ($element as $key => $value) {
                    if ($key == 'value') {
                        foreach ($value as $key => $v) {
                            if ($key == 'fields') {
                                foreach ($v as $key => $end) {
                                    if ($end['name'] == 'Таблица')
                                        foreach ($end as $key => $e) {
                                            if ($key == 'value') {
                                                foreach ($e as $key => $d) {
                                                    $rows = $e;
                                                }
                                            }
                                        }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $rows;
    }

    public function getDiscountedPrice($price, $sum, $bonus): int
    {
        $full_discount_percent = $sum == 0 ? 0 : $bonus * 100 / $sum;
        $product_discount = $price * $full_discount_percent / 100;

        return floor($price - $product_discount);
    }

    public function getFieldValue($task_fields)
    {


        foreach ($task_fields as $key => $element) {

            if ($element['id'] == 100) {
                foreach ($element as $key => $value) {
                    if ($key == 'value') {
                        foreach ($value as $key => $v) {
                            if ($key == 'fields') {
                                foreach ($v as $key => $end) {
                                    foreach ($end as $key => $e) {
                                        if ($e == 'Итого') {
                                            if (isset($end['value'])) {

                                                $money = $end['value'];
                                            }else{
                                                $money =null;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }


        return $money;
    }
    public function getUpd($task_fields)
    {
        foreach ($task_fields as $key => $element) {

            if ($element['id'] == 100) {
                foreach ($element as $key => $value) {
                    if ($key == 'value') {
                        foreach ($value as $key => $v) {
                            if ($key == 'fields') {
                                foreach ($v as $key => $end) {

                                    if ($end['id'] == 199) {
                                        if (isset($end['value'])) {

                                            $res = $end['value'];
                                        } else {
                                            $res = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $res;
    }
    public function getItems($tovar, $ship, $bonus, $money, $upd)
    {
        $result = [];
        $i = 0;
        foreach ($tovar as $key => $value) {
            $i += 1;
            $row_content = [
                "positionId" => $i,
                "name" => '',

                "quantity" => [
                    "value" => '',
                    "measure" => 'шт',
                ],
                "itemCode" => '',
                "itemPrice" => '',
                /* "itemAmount" => '', */
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

                                $row_content['itemPrice'] = $v1['value'] * 100;
                            }
                        }
                        if ($v1['name'] == 'Количество') {
                            foreach ($v1 as $key => $v2) {

                                $row_content['quantity']['value'] = $v1['value'];
                            }
                        }/*
                        if ($v1['name'] == 'Сумма') {
                            foreach ($v1 as $key => $v2) {

                                $row_content['itemAmount'] = $v1['value'];
                            }
                        } */
                        if ($v1['name'] == 'Код товара') {
                            foreach ($v1 as $key => $v2) {

                                $row_content['itemCode'] = $v1['value'];
                            }
                        }
                    }
                    // dd($row_content);
                }
            }

            array_push($result, $row_content);
        }
        $dis = $bonus * 100;
        $moneys = $money * 100;
        $sum = 0;

        for ($i = 0; $i < count($result); $i++) {

            $sum += (int) $result[$i]['itemPrice'] * (int)$result[$i]['quantity']['value'];
        }

        if ($dis != 0) {
            for ($i = 0; $i < count($result); $i++) {

                $result[$i]['itemPrice'] = $this->getDiscountedPrice($result[$i]['itemPrice'], $sum, $dis);

                //  dd($result[$i]['itemPrice']);
            }
        }

        // dd($result);

        $row_content = [
            "positionId" => $i + 1,
            "name" => 'Доставка',

            "quantity" => [
                "value" => '1',
                "measure" => 'шт',
            ],
            "itemCode" => '0',
            "itemPrice" => $ship * 100,

        ];



        array_push($result, $row_content);

        if ($ship == 0 && $upd != 0) {
            $row_content = [
                "positionId" => $i + 2,
                "name" => 'Доставка УПД',

                "quantity" => [
                    "value" => '1',
                    "measure" => 'шт',
                ],
                "itemCode" => '0',
                "itemPrice" => $upd * 100,

            ];

            array_push($result, $row_content);
        }

        $f = 0;
        for ($i = 0; $i < count($result); $i++) {

            $f +=  $result[$i]['itemPrice'] * $result[$i]['quantity']['value'];
        }

        if ($f != $moneys) {
            $result[$i - 1]['itemPrice'] += $moneys - $f;
        }

        //   dd($result);
        /*    $d = 0;
        for ($i = 0; $i < count($result); $i++) {

            $d +=  $result[$i]['itemPrice'] * $result[$i]['quantity']['value'];
        } */

        return $result;
    }
    public function getPaymentValue($task_fields)

    {
        foreach ($task_fields as $key => $element) {

            if ($element['id'] == 45) {
                foreach ($element as $key => $value) {
                    if ($key == 'value') {
                        foreach ($value as $key => $v) {
                            if ($key == 'fields') {
                                foreach ($v as $key => $end) {
                                    if ($end['id'] == 192)

                                        foreach ($end as $key => $e) {
                                            if ($key == 'value') {
                                                foreach ($e as $key => $d) {
                                                    if ($key == 'choice_id') {

                                                        $payment_method = $d;
                                                    }
                                                }
                                            }
                                        }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $payment_method;
    }

    public function SberId($task_fields)
    {
        foreach ($task_fields as $key => $v) {

            if ($v['id'] == 205) {
                $id = $v['id'];
            }
        }
        return $id;
    }
    public function getUrlFieldValue($task_fields)
    {

        foreach ($task_fields as $key => $v) {

            if ($v['id'] == 206) {
                if (!isset($v['value'])) {
                    return NULL;
                } else {
                    return $v['value'];
                }
            }
        }
    }
    public function getUrlFieldId($task_fields)
    {

        foreach ($task_fields as $key => $v) {

            if ($v['id'] == 206) {
                return $v['id'];
            }
        }
    }
    public function getPaymentMethod($task_fields)
    {
        foreach ($task_fields as $key => $element) {

            if ($element['id'] == 45) {
                foreach ($element as $key => $value) {
                    if ($key == 'value') {
                        foreach ($value as $key => $v) {
                            if ($key == 'fields') {
                                foreach ($v as $key => $end) {
                                    if ($end['id'] == 99)
                                        foreach ($end as $key => $e) {
                                            if ($key == 'value') {
                                                foreach ($e as $key => $d) {
                                                    if ($key == 'choice_id') {
                                                        $payment_method = $d;
                                                    }
                                                }
                                            }
                                        }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $payment_method;
    }
    public function getPaymentIdField($task_fields)
    {
        foreach ($task_fields as $key => $element) {

            if ($element['name'] == 'Оплата') {
                foreach ($element as $key => $value) {
                    if ($key == 'value') {
                        foreach ($value as $key => $v) {
                            if ($key == 'fields') {
                                foreach ($v as $key => $end) {
                                    $payment_method = $end['id'];
                                }
                            }
                        }
                    }
                }
            }
        }

        return $payment_method;
    }
}
