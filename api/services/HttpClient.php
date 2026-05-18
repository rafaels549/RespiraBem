<?php
namespace Rafael\RespiraBem\services;


class HttpClient
{
    public function makeRequest($url, $method = 'GET', $headers = [], $data = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($data) {

            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        
        $response = curl_exec($ch);
       $response =  json_decode($response, true);
        if(isset($response["cod"]) && $response["cod"] === 401)  {
            return [
                'error' => curl_error($ch),
                "success" => false
            ];
        }
        return [
            'data' => $response,
            "success" => true,
            "response" => $response
        ];
    }
}
