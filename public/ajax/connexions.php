<?php

ini_set('display_errors', 'On');
error_reporting(-1);

chdir('..');
require 'autoload.php';

class ConnexionsAjax extends Core
{
    public function __construct()
    {
    	parent::__construct();
        
        $this->run();
    }
    
    public function run()
    {
    	if ($user_id = $this->request->get('user_id', 'integer'))
        {
//$user_id = 100263;
            if ($user = $this->users->get_user($user_id))
            {
                $result = array();
                
                $result['phone_mobile'] = new StdClass();
                $result['phone_mobile']->search = $user->phone_mobile;                
                $result['phone_mobile']->found = array_filter($this->find_phone($user->id, $user->phone_mobile));
                
                $result['workphone'] = new StdClass();
                $result['workphone']->search = $user->workphone;                
                $result['workphone']->found = array_filter($this->find_phone($user->id, $user->workphone));
                
                $result['regaddress'] = new StdClass();
                $result['regaddress']->search = $user->Regregion.' '.$user->Regdistrict.' '.$user->Reglocality.' '.$user->Regcity.' '.$user->Regstreet.' '.$user->Reghousing.' '.$user->Regbuilding.' '.$user->Regroom;                
                $result['regaddress']->found = array_filter($this->find_address($user->id, $user->Regregion, $user->Regdistrict, $user->Reglocality, $user->Regcity, $user->Regstreet, $user->Reghousing, $user->Regbuilding, $user->Regroom));

                $result['faktaddress'] = new StdClass();
                $result['faktaddress']->search = $user->Faktregion.' '.$user->Faktdistrict.' '.$user->Faktlocality.' '.$user->Faktcity.' '.$user->Faktstreet.' '.$user->Fakthousing.' '.$user->Faktbuilding.' '.$user->Faktroom;                
                $result['faktaddress']->found = array_filter($this->find_address($user->id, $user->Faktregion, $user->Faktdistrict, $user->Faktlocality, $user->Faktcity, $user->Faktstreet, $user->Fakthousing, $user->Faktbuilding, $user->Faktroom));

                $result['contactperson1'] = new StdClass();
                $result['contactperson1']->search = $user->contact_person_phone;                
                $result['contactperson1']->fio = $user->contact_person_name;                
                $result['contactperson1']->found = array_filter($this->find_phone($user->id, $user->contact_person_phone));
                
                $result['contactperson2'] = new StdClass();
                $result['contactperson2']->search = $user->contact_person2_phone;                
                $result['contactperson2']->fio = $user->contact_person2_name;                
                $result['contactperson2']->found = array_filter($this->find_phone($user->id, $user->contact_person2_phone));
                


                $this->output($result);
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($result);echo '</pre><hr />';            
                
            }
            else
            {
                echo 'USER NOT FOUND';
            }
        }
        else
        {
            echo 'UNDEFINED USER ID';
        }
    }
    
    private function find_phone($user_id, $phone)
    {
        $prepare_phone = $this->prepare_phone($phone);

        $results = array();
        
        $query = $this->db->placehold("
            SELECT 
                id,
                lastname,
                firstname,
                patronymic,
                phone_mobile AS user_phone
            FROM __users
            WHERE id != ?
            AND REPLACE(REPLACE(REPLACE(REPLACE(phone_mobile, ' ', ''), '-', ''), '(', ''), ')', '') 
            LIKE '%".$this->db->escape($prepare_phone)."%'
        ", $user_id);
        $this->db->query($query);
        $results['users'] = $this->db->results();
        
        
        $query = $this->db->placehold("
            SELECT 
                id,
                lastname,
                firstname,
                patronymic,
                phone_mobile AS user_phone
            FROM __users
            WHERE id != ?
            AND REPLACE(REPLACE(REPLACE(REPLACE(workphone, ' ', ''), '-', ''), '(', ''), ')', '') 
            LIKE '%".$this->db->escape($prepare_phone)."%'
        ", $user_id);
        $this->db->query($query);
        $results['workphone'] = $this->db->results();
        
        
        $query = $this->db->placehold("
            SELECT 
                id,
                lastname,
                firstname,
                patronymic,
                phone_mobile AS user_phone
            FROM __users
            WHERE id != ?
            AND REPLACE(REPLACE(REPLACE(REPLACE(chief_phone, ' ', ''), '-', ''), '(', ''), ')', '') 
            LIKE '%".$this->db->escape($prepare_phone)."%'
        ", $user_id);
        $this->db->query($query);
        $results['chief_phone'] = $this->db->results();
        
        
        $results['contactpersons'] = array();
        $query = $this->db->placehold("
            SELECT 
                c.name AS cp_name,
                c.relation AS cp_relation,
                c.phone AS cp_phone,
                user_id,
                u.lastname,
                u.firstname,
                u.patronymic,
                u.phone_mobile AS user_phone
            FROM __contactpersons AS c
            LEFT JOIN __users AS u
            ON c.user_id = u.id
            WHERE c.user_id != ?
            AND REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') 
            LIKE '%".$this->db->escape($prepare_phone)."%'
        ", $user_id);
        $this->db->query($query);
        $results['contactpersons'] = array_merge($results['contactpersons'], $this->db->results());

        $query = $this->db->placehold("
            SELECT 
                contact_person_name AS cp_name,
                contact_person_relation AS cp_relation,
                contact_person_phone AS cp_phone,
                id AS user_id,
                lastname,
                firstname,
                patronymic,
                phone_mobile AS user_phone
            FROM __users
            WHERE id != ?
            AND REPLACE(REPLACE(REPLACE(REPLACE(contact_person_phone, ' ', ''), '-', ''), '(', ''), ')', '') 
            LIKE '%".$this->db->escape($prepare_phone)."%'
        ", $user_id);
        $this->db->query($query);
        $results['contactpersons'] = array_merge($results['contactpersons'], $this->db->results());

        $query = $this->db->placehold("
            SELECT 
                contact_person2_name AS cp_name,
                contact_person2_relation AS cp_relation,
                contact_person2_phone AS cp_phone,
                id AS user_id,
                lastname,
                firstname,
                patronymic,
                phone_mobile AS user_phone
            FROM __users
            WHERE id != ?
            AND REPLACE(REPLACE(REPLACE(REPLACE(contact_person2_phone, ' ', ''), '-', ''), '(', ''), ')', '') 
            LIKE '%".$this->db->escape($prepare_phone)."%'
        ", $user_id);
        $this->db->query($query);
        $results['contactpersons'] = array_merge($results['contactpersons'], $this->db->results());
        
        return $results;
    }
    
    private function find_address($user_id, $region, $district, $locality, $city, $street, $housing, $building, $room)
    {
        $results = array();
        
    	$query = $this->db->placehold("
            SELECT 
                id, 
                lastname,
                firstname,
                patronymic,
                phone_mobile
            FROM __users
            WHERE id != ?
            AND Regregion LIKE '%".$this->db->escape($region)."%'
            AND Regdistrict LIKE '%".$this->db->escape($district)."%'
            AND Reglocality LIKE '%".$this->db->escape($locality)."%'
            AND Regcity LIKE '%".$this->db->escape($city)."%'
            AND Regstreet LIKE '%".$this->db->escape($street)."%'
            AND Reghousing LIKE '%".$this->db->escape($housing)."%'
            AND Regbuilding LIKE '%".$this->db->escape($building)."%'
            AND Regroom LIKE '%".$this->db->escape($room)."%'
        ", $user_id);
        $this->db->query($query);
        
        $results['regaddress'] = $this->db->results();
        
        
    	$query = $this->db->placehold("
            SELECT 
                id, 
                lastname,
                firstname,
                patronymic,
                phone_mobile
            FROM __users
            WHERE id != ?
            AND Faktregion LIKE '%".$this->db->escape($region)."%'
            AND Faktdistrict LIKE '%".$this->db->escape($district)."%'
            AND Faktlocality LIKE '%".$this->db->escape($locality)."%'
            AND Faktcity LIKE '%".$this->db->escape($city)."%'
            AND Faktstreet LIKE '%".$this->db->escape($street)."%'
            AND Fakthousing LIKE '%".$this->db->escape($housing)."%'
            AND Faktbuilding LIKE '%".$this->db->escape($building)."%'
            AND Faktroom LIKE '%".$this->db->escape($room)."%'
        ", $user_id);
        $this->db->query($query);
        
        $results['faktaddress'] = $this->db->results();
        
        return $results;
    }
    
    
    private function prepare_phone($phone)
    {
        $prepare_phone = str_replace(array(' ', '-', '(', ')', '+'), '', $phone);
        $prepare_phone = substr($prepare_phone, -10);
        return $prepare_phone;
    }
    
    private function output($results)
    {
        $this->design->assign('results', $results);
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($results);echo '</pre><hr />';        
        echo $this->design->fetch('connexions.tpl');

    }
}
new ConnexionsAjax();