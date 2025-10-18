<?php
namespace Rafael\RespiraBem\services;
use Rafael\RespiraBem\interface\CurlInterface;

class Curl 
{
    public function makeCurl($url, $method = 'GET', $headers = [], $data = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $response = curl_exec($ch);
        if($response === false) {
            return [
                'error' => curl_error($ch),
                "success" => false
            ];
        }
        
        curl_close($ch);
        return [
            'data' => json_decode($response, true),
            "success" => true
        ];
    }
}
