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

        $response = self::curl($params);

        return $response;
    }
}