<?php

namespace App\Service;


class SmsService
{
    protected $token;

    public function __construct()
    {
        $this->token = "f69fc4102dc2f1cf2e3e82169708f431";
    }

    public function send($to, $message)
    {
        $url = "http://api.greenweb.com.bd/api.php?json";
        $data = array(
            'to' => "$to",
            'message' => "$message",
            'token' => "$this->token"
        );

        $ch = curl_init(); // Initialize cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $smsresult = curl_exec($ch);
        if ($smsresult) {
            return true;
        }
    }
}
