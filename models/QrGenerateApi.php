<?php

class QrGenerateApi extends Core
{
    protected $api_key = '2vJy2gwL.9VEXYUeji8C7EtUtxNhcRJWci45Zdejt';

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

        $ch = curl_init('https://app.devwapiserv.qrm.ooo/operations/qr-code/');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $qr_code = curl_exec($ch);
        curl_close($ch);

        echo '<pre>';
        var_dump(json_decode($qr_code));
    }
}