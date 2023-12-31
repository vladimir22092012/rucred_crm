<?php

class QrGenerateApi extends Core
{
    protected $api_key = 'baXhkN2j.dDYdzEIgnS4HPialSRx8uutqlwndmKtE';

    public function get_qr($sum, $qr_size)
    {
        $query =
            json_encode([
                'sum' => $sum,
                'qr_size' => $qr_size
            ]);

        $header =
            [
                "Content-Type: application/json",
                'X-Api-Key: ' . $this->api_key,
            ];

        $ch = curl_init('https://app.wapiserv.qrm.ooo/operations/qr-code/');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }
}