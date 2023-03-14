<?php

class FmsDb extends InfoSphere
{
    public static function sendRequest($user)
    {
        $params =
            [
                'UserID' => self::$userID,
                'Password' => self::$password,
                'sources' => 'fmsdb',
                'PersonReq' => [
                    'passport_series' => $user->passport_serial,
                    'passport_number' => $user->passport_number
                ]
            ];

        $request = self::curl($params);

        $fms = 'not found';

        if (isset($request['Source']) && $request['Source']['ResultsCount'] > 0) {
            foreach ($request['Source']['Record'] as $source) {
                foreach ($source as $field) {
                    if ($field['FieldName'] == 'ResultCode' && $field['FieldValue'] == 'VALID') {
                        if ($field['FieldValue'] == 'VALID')
                            $fms = 'valid';
                        else
                            $fms = 'invalid';

                    }
                }
            }
        }

        return $fms;
    }
}