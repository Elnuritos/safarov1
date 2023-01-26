<?php

namespace  App\Libraries;


use Illuminate\Support\Facades\Http;
use GuzzleHttp\HandlerStack;

class OneC
{
    public function WrtiteOff($query)
    {
        return Http::post(getenv('ONE_C_SEND'), $query)->json();
    }
}
