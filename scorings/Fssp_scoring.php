<?php

class Fssp_scoring extends Core
{
    private $user_id;
    private $order_id;
    private $audit_id;
    private $type;
    
    private $api_url = 'https://api-ip.fssp.gov.ru/api/v1.0/';
    private $api_key = '';
    
    private $error = null;
    
    
    public function __construct()
    {
    	parent::__construct();
        
        $this->api_key = $this->settings->apikeys['fssp']['api_key'];
    }
    
    public function run_scoring($scoring_id)
    {
        $update = array();
        
    	$scoring_type = $this->scorings->get_type('fssp');
        
        if ($scoring = $this->scorings->get_scoring($scoring_id))
        {
            if ($order = $this->orders->get_order((int)$scoring->order_id))
            {
                if (empty($order->lastname) || empty($order->firstname) || empty($order->patronymic) || empty($order->Regregion) || empty($order->birth))
                {
                    $update = array(
                        'status' => 'error',
                        'string_result' => 'в заявке не достаточно данных для проведения скоринга '.$order->lastname.' '.$order->firstname.' '.$order->patronymic.' '.$order->Regregion.' '.$order->birth
                    );
                }
                else
                {

                    $data = array(
                        'region' => $this->get_code($order->Regregion),
                        'lastname' => $order->lastname,
                        'firstname' => $order->firstname,
                        'secondname' => $order->patronymic,
                        'birthdate' => date('d.m.Y', strtotime($order->birth)),
                    );
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($data);echo '</pre><hr />';                    
                    $task = $this->create_task($data);
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($task);echo '</pre><hr />';
                    if (!empty($task->task))
                    {
                        do {
                            sleep(5);
                            $stat = $this->check_task($task->task);
                        } while (!empty($stat) && in_array($stat->status, array(1, 2)));
                        
                        $resp = $this->get_task($task->task);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';            
                        if (!empty($resp))
                        {
                            $debt = 0;
                            $pattern = '~([0-9.]*)\sруб~';
                            if (!empty($resp->result[0]->result))
                            {
                                foreach ($resp->result[0]->result as $item)
                                {
                                    preg_match_all($pattern, $item->subject, $founds);
                                    foreach ($founds[1] as $f)
                                        $debt += $f;
                                }
                            }
                            
                            $score = $debt < $scoring_type->params['amount'];
                            
                            $update = array(
                                'status' => 'completed',
                                'body' => serialize($resp),
                                'success' => $score,
                                'string_result' => 'Найденная сумма долга: '.$debt.' руб',
                            );
                            
                            $this->scorings->update_scoring($scoring_id, $update);
                            
                            $this->soap1c->send_fssp(empty($order->id_1c) ? $order->order_id : $order->id_1c, $resp);
                            
                            return $update;
                        }
                        else
                        {
                            if ($scoring->repeat_count < 2)
                            {
                                $update = array(
                                    'status' => 'repeat',
                                    'body' => 'Не удалось соединиться с сервером ФССП',
                                    'string_result' => 'ПОВТОРНЫЙ ЗАПРОС',
                                    'repeat_count' => $scoring->repeat_count + 1,
                                );
                                
                            }
                            else
                            {
                                $update = array(
                                    'status' => 'error',
                                    'body' => serialize($resp),
                                    'string_result' => 'Не удалось соединиться с сервером ФССП'
                                );
                            }    
                        }
                    }
                    else
                    {
                        if ($scoring->repeat_count < 2)
                        {
                            $update = array(
                                'status' => 'repeat',
                                'body' => 'При запросе произошла ошибка',
                                'string_result' => 'ПОВТОРНЫЙ ЗАПРОС',
                                'repeat_count' => $scoring->repeat_count + 1,
                            );
                            
                        }
                        else
                        {                            
                            $error = $this->get_error();
                            $update = array(
                                'status' => 'error',
                                'body' => serialize($error),
                                'string_result' => 'При запросе произошла ошибка'
                            );
                        }
                    }

                }
                
            }
            else
            {
                $update = array(
                    'status' => 'error',
                    'string_result' => 'не найдена заявка'
                );
            }
            
            if (!empty($update))
                $this->scorings->update_scoring($scoring_id, $update);
            
            return $update;

        }
    }
    


    public function run($audit_id, $user_id, $order_id)
    {
        $this->user_id = $user_id;
        $this->audit_id = $audit_id;
        $this->order_id = $order_id;
        
        $this->type = $this->scorings->get_type('fssp');

        $user = $this->users->get_user((int)$user_id);

        return $this->scoring($user->firstname, $user->patronymic, $user->lastname, $user->Regregion, $user->birth, $user->first_loan_amount, $user->phone_mobile);

    }

    private function scoring($firstname, $secondname, $lastname, $region_name, $birthday, $amount, $phone)
    {
        $data = array(
            'region' => $this->get_code($region_name),
            'lastname' => $lastname,
            'firstname' => $firstname,
            'secondname' => $secondname,
            'birthdata' => $birthday,
        );
        
        $task = $this->create_task($data);
        if (!empty($task->task))
        {
            do {
                usleep(2500);
                $stat = $this->check_task($task->task);
            } while (!empty($stat) && in_array($stat->status, array(1, 2)));
            
            $resp = $this->get_task($task->task);

            $debt = 0;
            $pattern = '~([0-9.]*)\sруб~';
            if (!empty($resp->result[0]->result))
            {
                foreach ($resp->result[0]->result as $item)
                {
                    preg_match_all($pattern, $item->subject, $founds);
                    foreach ($founds[1] as $f)
                        $debt += $f;
                }
            }
            
            $score = $debt < $this->type->params['amount'];
            
            $add_scoring = array(
                'user_id' => $this->user_id,
                'audit_id' => $this->audit_id,
                'type' => 'fssp',
                'body' => serialize($resp),
                'success' => (int)$score
            );
            if ($score)
            {
                $add_scoring['string_result'] = 'Долг < '.$this->type->params['amount'].' р';
            }
            else
            {
                $add_scoring['string_result'] = 'Долг > '.$this->type->params['amount'].' р';
            }

            $this->scorings->add_scoring($add_scoring);
            
            return $score;

        }
        else
        {
            $error = $this->get_error();
        }
    }


    
    public function create_task($data)
    {
        $resp = $this->send('search/physical', $data);
        
        return ($resp);
    }
    
    public function check_task($task_id)
    {
        $resp = $this->send('status', array('task' => $task_id));
        
        return $resp;
    }
    
    public function get_task($task_id)
    {
        $resp = $this->send('result', array('task' => $task_id));
        
        return $resp;    	
    }
    
    public function get_error()
    {
    	return $this->error;
    }
    
    
    public function send($method, $data)
    {
    	$this->error = null;
        
        $data['token'] = /*'hitrWj8fLR1d';*/$this->api_key;
        
        $url = $this->api_url. $method . '?' . http_build_query($data);
        
        $ch = curl_init($url);

//        curl_setopt($ch, CURLOPT_PROXY, '45.10.82.72:8000');
//        curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'KUoLnb:EXRvow');
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $json = curl_exec($ch);
        curl_close($ch);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump(date('H:i:s'), $json);echo '</pre><hr />';        
        $result = json_decode($json);
        if ($result->status != 'success')
        {
            $this->error = $result;
            return false;
        }
        
        return $result->response;
    }
    
    public function get_code($region_name)
    {
        $codes = array(
            1 => "адыгея",
            2 => "башкортостан",
            3 => "бурятия",
            4 => "алтай",
            5 => "дагестан",
            6 => "ингушетия",
            7 => "кабардино-балкарская",
            8 => "калмыкия",
            9 => "карачаево-черкесская",
            10 => "карелия",
            11 => "коми",
            12 => "марий эл",
            13 => "мордовия",
            14 => "саха /якутия/",
            15 => "северная осетия - алания",
            16 => "татарстан",
            17 => "тыва",
            18 => "удмуртская",
            19 => "хакасия",
            20 => "чеченская",
            21 => "чувашская",
            22 => "алтайский",
            23 => "краснодарский",
            24 => "красноярский",
            25 => "приморский",
            26 => "ставропольский",
            27 => "хабаровский", 
            28 => "амурская",
            29 => "архангельская",
            30 => "астраханская",
            31 => "белгородская",
            32 => "брянская",
            33 => "владимирская",
            34 => "волгоградская",
            35 => "вологодская",
            36 => "воронежская",
            37 => "ивановская",
            38 => "иркутская",
            39 => "калининградская",
            40 => "калужская",
            41 => "камчатский",
            42 => "кемеровская",
            43 => "кировская",
            44 => "костромская",
            45 => "курганская",
            46 => "курская",
            47 => "ленинградская",
            48 => "липецкая",
            49 => "магаданская",
            50 => "московская",
            51 => "мурманская",
            52 => "нижегородская",
            53 => "новгородская",
            54 => "новосибирская",
            55 => "омская",
            56 => "оренбургская",
            57 => "орловская",
            58 => "пензенская",
            59 => "пермский",
            60 => "псковская",
            61 => "ростовская",
            62 => "рязанская",
            63 => "самарская",
            64 => "саратовская",
            65 => "сахалинская",
            66 => "свердловская",
            67 => "смоленская",
            68 => "тамбовская",
            69 => "тверская",
            70 => "томская",
            71 => "тульская",
            72 => "тюменская",
            73 => "ульяновская",
            74 => "челябинская",
            75 => "забайкальский",
            76 => "ярославская",
            77 => "москва",
            78 => "санкт-петербург",
            82 => "крым",
            86 => "ханты-мансийский автономный округ - югра",
            87 => "чукотский",
            89 => "ямало-ненецкий",
            92 => "севастополь",
        );
        
        $index = array_search(mb_strtolower($region_name, 'utf8'), $codes);
        
        if (mb_strtolower($region_name, 'utf8') == 'еврейская')
            $index = 27;
        if (mb_strtolower($region_name, 'utf8') == 'ненецкий')
            $index = 29;
        if (mb_strtolower($region_name, 'utf8') == 'кемеровская область - кузбасс')
            $index = 42;
        
        return $index;            
    }
    
}