<?php

class Inn extends InfoSphere
{
    public static function sendRequest($user)
    {
        $params =
            [
                'UserID' => self::$userID,
                'Password' => self::$password,
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

        $inn = 'ИНН не найден';

        if(isset($request['Source']['@attributes']['checktype']) && $request['Source']['@attributes']['checktype'] != 'fns_inn')
            return $inn;

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

        return (int)$inn;
    }
}