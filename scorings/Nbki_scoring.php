<?php

class Nbki_scoring extends Core
{
    private $scoring_id;
    private $error = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function run_scoring($scoring_id)
    {
        if ($scoring = $this->scorings->get_scoring($scoring_id)) {
            $this->scoring_id = $scoring_id;

            if ($user = $this->users->get_user((int)$scoring->user_id)) {
                
                $regaddress = $this->addresses->get_address($user->regaddress_id);
                
                if ($regaddress->city) {
                    $city = $regaddress->city;
                } elseif ($regaddress->locality) {
                    $city = $regaddress->locality;
                } else {
                    $city = $regaddress->city;
                }

                return $this->scoring(
                    $user->firstname,
                    $user->patronymic,
                    $user->lastname,
                    $city,
                    $regaddress->street,
                    $user->birth,
                    $user->birth_place,
                    $user->passport_serial,
                    $user->passport_date,
                    $user->passport_issued,
                    $user->gender,
                    $user->client_status
                );
            } else {
                $update = array(
                    'status' => 'error',
                    'string_result' => 'не найден пользователь'
                );
                $this->scorings->update_scoring($scoring_id, $update);
                return $update;
            }
        }
    }

    public function scoring(
        $firstname,
        $patronymic,
        $lastname,
        $Regcity,
        $Regstreet,
        $birth,
        $birth_place,
        $passport_serial,
        $passport_date,
        $passport_issued,
        $gender,
        $client_status
    )
    {
        $genderArr = [
            'male' => 1,
            'female' => 2
        ];

        $json = '{
    "user": {
        "passport": {
            "series": "' . substr($passport_serial, 0, 4) . '",
            "number": "' . substr($passport_serial, 5) . '",
            "issued_date": "' . date('Y-m-d', strtotime($passport_date)) . '",
            "issued_by": "' . $passport_issued . '",
            "issued_city": "' . $Regcity . '"
        },
        "person": {
            "last_name": "' . $lastname . '",
            "first_name": "' . $firstname . '",
            "middle_name": "' . $patronymic . '",
            "birthday": "' . date('Y-m-d', strtotime($birth)) . '",
            "birthday_city": "' . $birth_place . '",
            "gender": ' . $genderArr[$gender] . '
        },
        "registration_address": {
            "city": "' . $Regcity . '",
            "street": "' . $Regstreet . '"
        }
    },
    "requisites": {
        "member_code": "VK01RR000000",
        "user_id": "VK01RR000002",
        "password": "Qe1kdjf1"
    }
}';
//var_dump($json);
//exit;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://45.147.176.183/api/nbki_test',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        //var_dump($response);
        //exit;

        curl_close($curl);
        $result = json_decode($response, true);


        if (!$result) {
            $add_scoring = array(
                'status' => 'completed',
                'body' => var_export($response),
                'success' => (int)$result,
                'string_result' => 'Ошибка запроса'
            );

            $this->scorings->update_scoring($this->scoring_id, $add_scoring);

            return $add_scoring;
        }

        if ($result['status'] == 'error') {
            if (json_encode($result['data']) == "No subject found for this inquiry") {
                $add_scoring = array(
                    'body' => '',
                    'status' => 'completed',
                    'success' => (int)true,
                    'string_result' => 'Неуспешный ответ: ' . 'субъект не найден',
                );
            } else {
                $add_scoring = array(
                    'body' => '',
                    'status' => 'completed',
                    'success' => (int)false,
                    'string_result' => 'Неуспешный ответ: ' . json_encode($result['data'], JSON_UNESCAPED_UNICODE)
                );
            }


            $this->scorings->update_scoring($this->scoring_id, $add_scoring);

            return $add_scoring;
        }

        switch ($client_status) {
            case 'nk':
                $number_of_active_max = $this->settings->nbki_number_of_active_max_nk;
                $number_of_active = $this->settings->nbki_number_of_active_nk;
                $share_of_unknown = $this->settings->nbki_share_of_unknown_nk;
                $share_of_overdue = $this->settings->nbki_share_of_overdue_nk;
                break;

            case 'pk':
                $number_of_active_max = $this->settings->nbki_number_of_active_max_pk;
                $number_of_active = $this->settings->nbki_number_of_active_pk;
                $share_of_unknown = $this->settings->nbki_share_of_unknown_pk;
                $share_of_overdue = $this->settings->nbki_share_of_overdue_pk;
                break;

            default:
                $number_of_active_max = $this->settings->nbki_number_of_active_max_nk;
                $number_of_active = $this->settings->nbki_number_of_active_nk;
                $share_of_unknown = $this->settings->nbki_share_of_unknown_nk;
                $share_of_overdue = $this->settings->nbki_share_of_overdue_nk;
                break;
        }


        if ($result['number_of_active'] >= $number_of_active_max) {
            $add_scoring = array(
                'status' => 'completed',
                'body' => serialize($result['data']),
                'success' => 0,
                'string_result' => 'превышен допустимый порог активных займов'
            );

            $this->scorings->update_scoring($this->scoring_id, $add_scoring);

            return $add_scoring;
        }

        if ($result['number_of_active'] >= $number_of_active) {
            if ($result['share_of_overdue'] >= $share_of_overdue || $result['share_of_unknown'] >= $share_of_unknown) {
                $add_scoring = array(
                    'status' => 'completed',
                    'body' => serialize($result['data']),
                    'success' => 0,
                    'string_result' => 'превышен допустимый порог доли просроченных или неизвестных займов'
                );

                $this->scorings->update_scoring($this->scoring_id, $add_scoring);

                return $add_scoring;
            }
        }

        if ($result['share_of_unknown'] > $share_of_unknown) {
            $add_scoring = array(
                'status' => 'completed',
                'body' => serialize($result['data']),
                'success' => 0,
                'string_result' => 'превышен допустимый порог доли неизвестных займов'
            );

            $this->scorings->update_scoring($this->scoring_id, $add_scoring);

            return $add_scoring;
        }

        $add_scoring = array(
            'status' => 'completed',
            'body' => serialize($result['data']),
            'success' => 1,
            'string_result' => 'Проверки пройдены'
        );

        $this->scorings->update_scoring($this->scoring_id, $add_scoring);

        return $add_scoring;
    }
}