<?php

class Fssp extends InfoSphere
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

        $result = ['status' => 'error', 'success' => 0];

        $expSum = 0;
        $badArticle = [];

        if ($request['Source']['ResultsCount'] > 0) {
            if (isset($request['Source']['Record'])) {
                foreach ($request['Source']['Record'] as $sources) {

                    if (isset($sources['Field'])) {
                        foreach ($sources['Field'] as $source) {
                            if ($source['FieldName'] == 'Total')
                                $expSum += $source['FieldValue'];

                            if ($source['FieldName'] == 'CloseReason1' && in_array($source['FieldValue'], [46, 47]))
                                $badArticle[] = $source['FieldValue'];
                        }
                    } else {
                        foreach ($sources as $source) {
                            if ($source['FieldName'] == 'Total')
                                $expSum += $source['FieldValue'];

                            if ($source['FieldName'] == 'CloseReason1' && in_array($source['FieldValue'], [46, 47]))
                                $badArticle[] = $source['FieldValue'];
                        }
                    }
                }
            }

            $maxExp = new Scorings();
            $maxExp = $maxExp->get_type(3);
            $maxExp = $maxExp->params;
            $maxExp = $maxExp['amount'];

            if ($expSum > 0)
                $result = ['overdueSum' => 'Сумма долга: ' . $expSum];
            else
                $result = ['overdueSum' => 'Долгов нет'];

            if ($expSum > $maxExp || !empty($badArticle)) {

                if (!empty($badArticle)) {
                    $articles = implode(',', array_unique($badArticle));
                    $result['articles'] = '<br>Обнаружены статьи: ' . $articles;
                }

                $result['success'] = 0;
            } else {
                $result['success'] = 1;
            }
        } else {
            $result['success'] = 1;
            $result['status'] = 'Долгов нет';
        }

        return $result;
    }
}