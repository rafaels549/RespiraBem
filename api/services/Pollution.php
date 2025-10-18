<?php

namespace Rafael\RespiraBem\services;

use Rafael\RespiraBem\services\Curl;

class Pollution
{
    public $curl;
    private $apiKey;

    public function __construct(Curl $curl, $apiKey)
    {
        $this->curl = $curl;
        $this->apiKey = $apiKey;
    }

    public function getPollutionData(float $lat, float $lon): array {
        return $this->curl->makeCurl("http://api.openweathermap.org/data/2.5/air_pollution?lat=$lat&lon=$lon&appid=" . $this->apiKey);
    }
}
