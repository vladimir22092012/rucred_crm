<?php

class Mvd extends InfoSphere
{
    public static function sendRequest($user)
    {
        $params =
            [
                'UserID' => self::$userID,
                'Password' => self::$password,
                'sources' => 'mvd',
                'PersonReq' => [
                    'first' => $user->firstname,
                    'middle' => $user->patronymic,
                    'paternal' => $user->lastname,
                    'birthDt' => date('Y-m-d', strtotime($user->birth))
                ]
            ];

        $request = self::curl($params);

        $mvd = 'not found';

        if (isset($request['Source']) && $request['Source']['ResultsCount'] > 0) {
            foreach ($request['Source']['Record'] as $source) {
                foreach ($source as $field) {
                    if ($field['FieldName'] == 'ResultCode') {
                        if ($field['FieldValue'] == 'FOUND')
                            $mvd = 'found';
                    }
                }
            }
        }

        return $mvd;
    }
}