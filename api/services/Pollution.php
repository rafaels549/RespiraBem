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

    public function getPollutionDataOpenMeteo(float $lat, float $lon): array {
        return $this->httpClient->makeRequest("https://air-quality-api.open-meteo.com/v1/air-quality?latitude=$lat&longitude=$lon&current=carbon_monoxide,nitrogen_dioxide,ozone,sulphur_dioxide,pm2_5,pm10");
    }
}
