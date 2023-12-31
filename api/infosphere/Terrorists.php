<?php

class Terrorists extends InfoSphere
{
    public static function sendRequest($user)
    {
        $params =
            [
                'UserID' => self::$userID,
                'Password' => self::$password,
                'sources' => 'fssp',
                'PersonReq' => [
                    'first' => $user->firstname,
                    'middle' => $user->patronymic,
                    'paternal' => $user->lastname,
                    'birthDt' => date('Y-m-d', strtotime($user->birth))
                ]
            ];

        $request = self::curl($params);

        if (!isset($request['Source']))
            return 'error';

        if ($request['Source']['ResultsCount'] == 1)
            return 'found';
        else
            return 'not found';
    }
}