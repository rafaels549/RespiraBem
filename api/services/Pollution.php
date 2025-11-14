<?php

namespace Rafael\RespiraBem\services;

use Rafael\RespiraBem\services\HttpClient;

class Pollution
{
    public $httpClient;
    private $apiKey;

    public function __construct(HttpClient $httpClient,$apiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }

    public function getPollutionData(float $lat, float $lon): array {
        return $this->httpClient->makeRequest("http://api.openweathermap.org/data/2.5/air_pollution?lat=$lat&lon=$lon&appid=" . $this->apiKey);
    }
}
