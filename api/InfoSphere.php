<?php

class InfoSphere implements ApiInterface
{

    public static function sendRequest($user)
    {
        $params =
            [
                'UserID' => 'barvil',
                'Password' => 'KsetM+H5',
                'sources' => 'fns',
                'PersonReq' => [
                    'first' => $user->firstname,
                    'middle' => $user->patronymic,
                    'paternal' => $user->lastname,
                    'birthDt' => date('Y-m-d', strtotime($user->birth)),
                    'passport_series' => $user->passport_serial,
                    'passport_number' => $user->passport_number
                ]
            ];

        $request = self::curl($params);

        $inn = 0;

        foreach ($request['Source'] as $sources) {
            if ($sources['@attributes']['checktype'] == 'fns_inn') {
                foreach ($sources['Record'] as $fields) {
                    foreach ($fields as $field) {
                        if ($field['FieldName'] == 'INN')
                            $inn = $field['FieldValue'];
                    }
                }
            }
        }

        UsersORM::where('id', $user->id)->update(['inn' => $inn]);

        return 1;
    }

    private static function curl($params)
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