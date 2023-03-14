<?php

abstract class InfoSphere implements ApiInterface
{
    protected static $userID = 'rucred_api';
    protected static $password = 'dHoL*0TI';

    abstract static function sendRequest($user);

    protected static function curl($params)
    {
        $xml = new XMLSerializer();
        $request = $xml->serialize($params);

        $ch = curl_init('https://i-sphere.ru/2.00/');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $html = curl_exec($ch);
        $html = simplexml_load_string($html);
        $json = json_encode($html);
        $array = json_decode($json, TRUE);
        curl_close($ch);

        return $array;
    }
}