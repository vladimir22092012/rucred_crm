<?php

class QrGenerateApi extends Core
{
    protected $api_key = 'ZJIMfrDe.JzXXmEhPrPeV3ubL4ZlOzyOhpt559L9S';

    public function get_qr($sum, $qr_size)
    {
        $query =
            [
                'sum' => $sum,
                'qr_size' => $qr_size
            ];

        $ch = curl_init('https://stage.wapiserv.qrm.ooo/operations/qr-code/');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            "Content-Type: application/json",
            'X-Api-Key:'. $this->api_key,
            "X-CSRFToken:". md5(microtime())
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $qr_code = curl_exec($ch);

        var_dump($qr_code);
    }
}