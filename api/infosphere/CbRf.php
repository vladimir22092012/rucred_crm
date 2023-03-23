<?php

class CbRf extends InfoSphere
{
    public static function sendRequest($user)
    {
        $params =
            [
                'UserID' => self::$userID,
                'Password' => self::$password,
                'sources' => 'fns',
                'PersonReq' => [
                    'inn' => $user->inn,
                ]
            ];

        $request = self::curl($params);

        return $request;
    }
}