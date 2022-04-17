<?php

class Orders extends Core
{
    private $statuses = array(
        0 => 'Новая',
        1 => 'Р.Принята',
        //2 => 'В обработке',
        3 => 'Р.Подтверждена',
        4 => 'Одобрена',
        5 => 'Принята',
        6 => 'Одобрена',
        7 => 'Отказ',
        8 => 'Подписан',
        9 => 'Выдан',
        10 => 'Отказ клиента',
        11 => 'Не удалось выдать',
        12 => 'Черновик'
    );
    
    public function get_statuses()
    {
        return $this->statuses;
    }
    
	public function get_order($id)
	{
		$query = $this->db->placehold("
            SELECT 
                o.id AS order_id,
                o.uid,
                o.contract_id,
                o.manager_id,
                o.date,
                o.user_id,
                o.card_id,
                o.ip,
                o.amount,
                o.period,
                o.status,
                o.first_loan,
                o.reject_reason,
                o.reason_id,
                o.id_1c,
                o.status_1c,
                o.utm_source,
                o.utm_medium,
                o.utm_campaign,
                o.utm_content,
                o.utm_term,
                o.webmaster_id,
                o.click_hash,
                o.juicescore_session_id,
                o.local_time,
                o.autoretry,
                o.autoretry_result,
                o.autoretry_summ,
                o.accept_date,
                o.reject_date,
                o.approve_date,
                o.confirm_date,
                o.client_status,
                o.antirazgon,
                o.antirazgon_date,
                o.antirazgon_amount,
                o.offline,
                o.offline_point_id,
                o.penalty_date,
                o.quality_workout,
                o.percent,
                o.charge,
                o.insure,
                o.bot_inform,
                o.sms_inform,
                o.employer,
                o.loan_type,
                o.probably_return_date,
                o.probably_return_sum,
                u.UID AS user_uid,
                u.service_sms,
                u.service_insurance,
                u.service_reason,
                u.phone_mobile,
                u.email,
                u.lastname,
                u.firstname,
                u.patronymic,
                u.gender,
                u.birth,
                u.birth_place,
                u.passport_serial,
                u.subdivision_code,
                u.passport_date,
                u.passport_issued,
                u.snils,
                u.inn,
                u.Regadressfull,
                u.Regindex,
                u.Regregion,
                u.Regregion_shorttype,
                u.Regdistrict,
                u.Regdistrict_shorttype,
                u.Reglocality,
                u.Reglocality_shorttype,
                u.Regcity,
                u.Regcity_shorttype,
                u.Regstreet,
                u.Regstreet_shorttype,
                u.Reghousing,
                u.Regbuilding,
                u.Regroom,
                u.Faktadressfull,
                u.Faktindex,
                u.Faktregion,
                u.Faktregion_shorttype,
                u.Faktdistrict,
                u.Faktdistrict_shorttype,
                u.Faktlocality,
                u.Faktlocality_shorttype,
                u.Faktcity,
                u.Faktcity_shorttype,
                u.Faktstreet,
                u.Faktstreet_shorttype,
                u.Fakthousing,
                u.Faktbuilding,
                u.Faktroom,
                u.contact_person_name,
                u.contact_person_phone,
                u.contact_person_relation,
                u.contact_person2_name,
                u.contact_person2_phone,
                u.contact_person2_relation,
                u.contact_person3_name,
                u.contact_person3_phone,
                u.contact_person3_relation,
                u.workplace,
                u.workcomment,
                u.workaddress,
                u.profession,
                u.workphone,
                u.chief_name,
                u.chief_position,
                u.chief_phone,
                u.income,
                u.expenses,
                u.social,
                u.contact_status,
                u.profunion,
                u.prev_fio,
                u.fio_change_date,
                u.sex,
                u.fio_spouse,
                u.phone_spouse,
                u.foreign_flag,
                u.foreign_husb_wife,
                u.fio_public_spouse,
                u.foreign_relative,
                u.fio_relative,
                u.fio_acc_holder,
                u.account_number,
                u.bank_name,
                u.bik_bank,
                u.viber_num,
                u.telegram_num,
                u.whatsapp_num,
                u.push_not,
                u.sms_not,
                u.email_not,
                u.massanger_not
            FROM __orders AS o
            LEFT JOIN __users AS u
            ON u.id = o.user_id
            WHERE o.id = ?
        ", (int)$id);
        $this->db->query($query);
        $result = $this->db->result();

        return $result;
    }

    public function last_order_number($user_id)
    {
        $query = $this->db->placehold("
        SELECT `number`
        FROM s_orders
        where user_id = ?
        order by id desc limit 1
        ", (int)$user_id);

        $this->db->query($query);
        $results = $this->db->result('number');

        return $results;
    }
    
	public function get_orders($filter = array())
	{
		$id_filter = '';
		$offline_filter = '';
		$user_id_filter = '';
		$status_filter = '';
        $type_filter = '';
        $manager_id_filter = '';
        $date_from_filter = '';
        $date_to_filter = '';
        $issuance_date_from_filter = '';
        $issuance_date_to_filter = '';
        $search_filter = '';
        $keyword_filter = '';
        $current_filter = '';
        $autoretry_filter = '';
        $client_filter = '';
        $limit = 10000;
		$page = 1;
        $workout_sort = '';
        $sort = 'order_id DESC';
        
        if (!empty($filter['id']))
            $id_filter = $this->db->placehold("AND o.id IN (?@)", array_map('intval', (array)$filter['id']));
            
        if (!empty($filter['status']))
            $status_filter = $this->db->placehold("AND o.status IN (?@)", (array)$filter['status']);
        
        if (!empty($filter['client']))
        {
            switch ($filter['client']):
                case 'new':
                    $client_filter = $this->db->placehold("AND o.client_status = 'nk'");
                break;
                case 'repeat':
                    $client_filter = $this->db->placehold("AND o.client_status = 'rep'");
                break;
                case 'pk':
                    $client_filter = $this->db->placehold("AND (o.client_status = 'pk' OR o.client_status = 'crm')");
                break;
            endswitch;
        }
        
        if (!empty($filter['type']))
            $type_filter = $this->db->placehold("AND o.type IN (?@)", (array)$filter['type']);
        
        if (!empty($filter['user_id']))
            $user_id_filter = $this->db->placehold("AND o.user_id IN (?@)", array_map('intval', (array)$filter['user_id']));
        
        if (isset($filter['offline']))
            $offline_filter = $this->db->placehold("AND o.offline = ?", (int)$filter['offline']);
        
        if (!empty($filter['date_from']))
            $date_from_filter = $this->db->placehold("AND DATE(o.date) >= ?", $filter['date_from']);
            
        if (!empty($filter['date_to']))
            $date_to_filter = $this->db->placehold("AND DATE(o.date) <= ?", $filter['date_to']);

        if (!empty($filter['issuance_date_from']))
            $issuance_date_from_filter = $this->db->placehold("AND o.id IN (SELECT order_id FROM __contracts WHERE DATE(inssuance_date) >= ?)", $filter['issuance_date_from']);
            
        if (!empty($filter['issuance_date_to']))
            $issuance_date_to_filter = $this->db->placehold("AND o.id IN (SELECT order_id FROM __contracts WHERE DATE(inssuance_date) <= ?)", $filter['issuance_date_to']);

        if (isset($filter['autoretry']))
            $autoretry_filter = $this->db->placehold("AND o.autoretry = ?", (int)$filter['autoretry']);
        
        if (!empty($filter['search']))
        {
            if (!empty($filter['search']['order_id']))
                $search_filter .= $this->db->placehold(' AND (o.id = ? OR o.id IN(SELECT order_id FROM __contracts WHERE number LIKE "%'.$this->db->escape($filter['search']['order_id']).'%"))', (int)$filter['search']['order_id']);
            if (!empty($filter['search']['date']))
                $search_filter .= $this->db->placehold(' AND DATE(o.date) = ?', date('Y-m-d', strtotime($filter['search']['date'])));
            if (!empty($filter['search']['amount']))
                $search_filter .= $this->db->placehold(' AND o.amount = ?', (int)$filter['search']['amount']);
            if (!empty($filter['search']['period']))
                $search_filter .= $this->db->placehold(' AND o.period = ?', (int)$filter['search']['period']);
            if (!empty($filter['search']['employer']))
                $search_filter .= $this->db->placehold(' AND o.employer LIKE "%'.$this->db->escape($filter['search']['employer']).'%"');
            if (!empty($filter['search']['fio']))
            {
                $fio_filter = array();
                $expls = array_map('trim', explode(' ', $filter['search']['fio']));
                $search_filter .= $this->db->placehold(' AND (');
                foreach ($expls as $expl)
                {
                    $expl = $this->db->escape($expl);
                    $fio_filter[] = $this->db->placehold("(u.lastname LIKE '%".$expl."%' OR u.firstname LIKE '%".$expl."%' OR u.patronymic LIKE '%".$expl."%')");
                }
                $search_filter .= implode(' AND ', $fio_filter);
                $search_filter .= $this->db->placehold(')');
            }
            if (!empty($filter['search']['birth']))
                $search_filter .= $this->db->placehold(' AND DATE(u.birth) = ?', date('Y-m-d', strtotime($filter['search']['birth'])));
            if (!empty($filter['search']['phone']))
                $search_filter .= $this->db->placehold(" AND u.phone_mobile LIKE '%".$this->db->escape(str_replace(array(' ', '-', '(', ')', '+'), '', $filter['search']['phone']))."%'");
            if (!empty($filter['search']['region']))
                $search_filter .= $this->db->placehold(" AND u.Regregion LIKE '%".$this->db->escape($filter['search']['region'])."%'");
            if (!empty($filter['search']['status']))
                $search_filter .= $this->db->placehold(" AND o.1c_status LIKE '%".$this->db->escape($filter['search']['status'])."%'");
            
            if (!empty($filter['search']['manager_id']))
            {
                if ($filter['search']['manager_id'] == 'none')
                    $search_filter .= $this->db->placehold(" AND (o.manager_id = 0 OR o.manager_id IS NULL)");                
                else
                    $search_filter .= $this->db->placehold(" AND o.manager_id = ?", (int)$filter['search']['manager_id']);
            }
        }
        
		if(isset($filter['keyword']))
		{
			$keywords = explode(' ', $filter['keyword']);
			foreach($keywords as $keyword)
				$keyword_filter .= $this->db->placehold('AND (o.name LIKE "%'.$this->db->escape(trim($keyword)).'%" )');
		}
        
        if (!empty($filter['current']))
            $current_filter = $this->db->placehold("AND (o.manager_id = ? OR (o.manager_id IS NULL AND o.status = 0))", (int)$filter['current']);

		if(isset($filter['limit']))
			$limit = max(1, intval($filter['limit']));

		if(isset($filter['page']))
			$page = max(1, intval($filter['page']));
            
        $sql_limit = $this->db->placehold(' LIMIT ?, ? ', ($page-1)*$limit, $limit);
        
        if (!empty($filter['sort']))
        {
            switch ($filter['sort']):
                
                case 'order_id_asc':
                    $sort = 'order_id ASC';
                break;
                
                case 'order_id_desc':
                    $sort = 'order_id DESC';
                break;
                
                case 'date_asc':
                    $sort = 'o.date ASC';
                break;
                
                case 'date_desc':
                    $sort = 'o.date DESC';
                break;
                
                case 'amount_desc':
                    $sort = 'o.amount DESC';
                break;
                
                case 'amount_asc':
                    $sort = 'o.amount ASC';
                break;
                
                case 'period_asc':
                    $sort = 'o.period ASC';
                break;
                
                case 'period_desc':
                    $sort = 'o.period DESC';
                break;
                
                case 'fio_asc':
                    $sort = 'u.lastname ASC';
                break;
                
                case 'fio_desc':
                    $sort = 'u.lastname DESC';
                break;
                                
                case 'birth_asc':
                    $sort = 'u.birth ASC';
                break;
                
                case 'birth_desc':
                    $sort = 'u.birth DESC';
                break;

                case 'phone_asc':
                    $sort = 'u.phone_mobile ASC';
                break;
                
                case 'phone_desc':
                    $sort = 'u.phone_mobile DESC';
                break;
                                
                case 'region_asc':
                    $sort = 'u.Regregion ASC';
                break;
                
                case 'region_desc':
                    $sort = 'u.Regregion DESC';
                break;
                                
                case 'status_asc':
                    $sort = 'o.1c_status ASC';
                break;
                
                case 'status_desc':
                    $sort = 'o.1c_status DESC';
                break;
                                
                case 'penalty_asc':
                    $sort = 'o.penalty_date ASC';
                break;
                
                case 'penalty_desc':
                    $sort = 'o.penalty_date DESC';
                break;
                                
            endswitch;
        }
        
        if (!empty($filter['workout_sort']))
            $workout_sort = 'o.quality_workout ASC, ';
        
        $query = $this->db->placehold("
            SELECT 
                o.id AS order_id,
                o.uid,
                o.contract_id,
                o.manager_id,
                o.date,
                o.user_id,
                o.card_id,
                o.ip,
                o.amount,
                o.period,
                o.status,
                o.first_loan,
                o.reject_reason,
                o.reason_id,
                o.id_1c,
                o.status_1c,
                o.utm_source,
                o.utm_medium,
                o.utm_campaign,
                o.utm_content,
                o.utm_term,
                o.webmaster_id,
                o.click_hash,
                o.juicescore_session_id,
                o.local_time,
                o.autoretry,
                o.autoretry_result,
                o.autoretry_summ,
                o.accept_date,
                o.reject_date,
                o.approve_date,
                o.confirm_date,
                o.client_status,
                o.antirazgon,
                o.antirazgon_date,
                o.antirazgon_amount,
                o.offline,
                o.offline_point_id,
                o.penalty_date,
                o.quality_workout,
                o.percent,
                o.charge,
                o.insure,
                o.bot_inform,
                o.sms_inform,
                o.employer,
                o.loan_type,
                u.UID AS user_uid,
                u.service_sms,
                u.service_insurance,
                u.service_reason,
                u.phone_mobile,
                u.email,
                u.lastname,
                u.firstname,
                u.patronymic,
                u.gender,
                u.birth,
                u.birth_place,
                u.passport_serial,
                u.subdivision_code,
                u.passport_date,
                u.passport_issued,
                u.snils,
                u.inn,
                u.Regindex,
                u.Regregion,
                u.Regregion_shorttype,
                u.Regdistrict,
                u.Regdistrict_shorttype,
                u.Reglocality,
                u.Reglocality_shorttype,
                u.Regcity,
                u.Regcity_shorttype,
                u.Regstreet,
                u.Regstreet_shorttype,
                u.Reghousing,
                u.Regbuilding,
                u.Regroom,
                u.Faktindex,
                u.Faktregion,
                u.Faktregion_shorttype,
                u.Faktdistrict,
                u.Faktdistrict_shorttype,
                u.Faktlocality,
                u.Faktlocality_shorttype,
                u.Faktcity_shorttype,
                u.Faktcity,
                u.Faktstreet_shorttype,
                u.Faktstreet,
                u.Fakthousing,
                u.Faktbuilding,
                u.Faktroom,
                u.contact_person_name,
                u.contact_person_phone,
                u.contact_person_relation,
                u.contact_person2_name,
                u.contact_person2_phone,
                u.contact_person2_relation,
                u.contact_person3_name,
                u.contact_person3_phone,
                u.contact_person3_relation,
                u.workplace,
                u.workaddress,
                u.workcomment,
                u.profession,
                u.workphone,
                u.chief_name,
                u.chief_position,
                u.chief_phone,
                u.income,
                u.expenses,
                u.social,
                u.contact_status,
                u.loan_history
            FROM __orders AS o
            LEFT JOIN __users AS u
            ON u.id = o.user_id
            WHERE 1
                $id_filter
                $offline_filter
                $user_id_filter
                $status_filter
                $type_filter
                $date_from_filter
                $date_to_filter
                $issuance_date_from_filter
                $issuance_date_to_filter
                $search_filter
                $keyword_filter
                $current_filter
                $autoretry_filter
                $client_filter
            ORDER BY $workout_sort $sort 
            $sql_limit
        ");
        $this->db->query($query);
        if ($results = $this->db->results())
        {
            foreach ($results as $result)
            {
                $result->loan_history = json_decode($result->loan_history);
            }
        }
if ($this->is_developer){
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($query, $results);echo '</pre><hr />';
}
        return $results;
	}
    
	public function count_orders($filter = array())
	{
        $id_filter = '';
        $offline_filter = '';
        $status_filter = '';
        $type_filter = '';
        $date_from_filter = '';
        $date_to_filter = '';
        $issuance_date_from_filter = '';
        $issuance_date_to_filter = '';
        $search_filter = '';
        $current_filter = '';
        $autoretry_filter = '';
        $client_filter = '';
        $keyword_filter = '';
        
        if (!empty($filter['id']))
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));
		
        if (isset($filter['offline']))
            $offline_filter = $this->db->placehold("AND o.offline = ?", (int)$filter['offline']);
        
        if (!empty($filter['status']))
            $status_filter = $this->db->placehold("AND o.status IN (?@)", (array)$filter['status']);
        
        if (!empty($filter['type']))
            $type_filter = $this->db->placehold("AND o.type IN (?@)", (array)$filter['type']);
        
        if (!empty($filter['date_from']))
            $date_from_filter = $this->db->placehold("AND DATE(o.date) >= ?", $filter['date_from']);
            
        if (!empty($filter['date_to']))
            $date_to_filter = $this->db->placehold("AND DATE(o.date) <= ?", $filter['date_to']);

        if (!empty($filter['issuance_date_from']))
            $issuance_date_from_filter = $this->db->placehold("AND o.id IN (SELECT order_id FROM __contracts WHERE DATE(inssuance_date) >= ?)", $filter['issuance_date_from']);
            
        if (!empty($filter['issuance_date_to']))
            $issuance_date_to_filter = $this->db->placehold("AND o.id IN (SELECT order_id FROM __contracts WHERE DATE(inssuance_date) <= ?)", $filter['issuance_date_to']);

        if (!empty($filter['search']))
        {
            if (!empty($filter['search']['order_id']))
                $search_filter .= $this->db->placehold(' AND (o.id = ? OR o.id IN(SELECT order_id FROM __contracts WHERE number LIKE "%'.$this->db->escape($filter['search']['order_id']).'%"))', (int)$filter['search']['order_id']);
            if (!empty($filter['search']['date']))
                $search_filter .= $this->db->placehold(' AND DATE(o.date) = ?', date('Y-m-d', strtotime($filter['search']['date'])));
            if (!empty($filter['search']['amount']))
                $search_filter .= $this->db->placehold(' AND o.amount = ?', (int)$filter['search']['amount']);
            if (!empty($filter['search']['period']))
                $search_filter .= $this->db->placehold(' AND o.period = ?', (int)$filter['search']['period']);
            if (!empty($filter['search']['employer']))
                $search_filter .= $this->db->placehold(' AND o.employer LIKE "%'.$this->db->escape($filter['search']['employer']).'%"');
            if (!empty($filter['search']['fio']))
            {
                $fio_filter = array();
                $expls = array_map('trim', explode(' ', $filter['search']['fio']));
                $search_filter .= $this->db->placehold(' AND (');
                foreach ($expls as $expl)
                {
                    $expl = $this->db->escape($expl);
                    $fio_filter[] = $this->db->placehold("(u.lastname LIKE '%".$expl."%' OR u.firstname LIKE '%".$expl."%' OR u.patronymic LIKE '%".$expl."%')");
                }
                $search_filter .= implode(' AND ', $fio_filter);
                $search_filter .= $this->db->placehold(')');
            }
            if (!empty($filter['search']['birth']))
                $search_filter .= $this->db->placehold(' AND DATE(u.birth) = ?', date('Y-m-d', strtotime($filter['search']['birth'])));
            if (!empty($filter['search']['phone']))
                $search_filter .= $this->db->placehold(" AND u.phone_mobile LIKE '%".$this->db->escape(str_replace(array(' ', '-', '(', ')', '+'), '', $filter['search']['phone']))."%'");
            if (!empty($filter['search']['region']))
                $search_filter .= $this->db->placehold(" AND u.Regregion LIKE '%".$this->db->escape($filter['search']['region'])."%'");
             if (!empty($filter['search']['status']))
                $search_filter .= $this->db->placehold(" AND o.1c_status LIKE '%".$this->db->escape($filter['search']['status'])."%'");
            if (!empty($filter['search']['manager_id']))
            {
                if ($filter['search']['manager_id'] == 'none')
                    $search_filter .= $this->db->placehold(" AND (o.manager_id = 0 OR o.manager_id IS NULL)");                
                else
                    $search_filter .= $this->db->placehold(" AND o.manager_id = ?", (int)$filter['search']['manager_id']);
            }
        }
        
        if(isset($filter['keyword']))
		{
			$keywords = explode(' ', $filter['keyword']);
			foreach($keywords as $keyword)
				$keyword_filter .= $this->db->placehold('AND (name LIKE "%'.$this->db->escape(trim($keyword)).'%" )');
		}

        if (!empty($filter['client']))
        {
            switch ($filter['client']):
                case 'new':
                    $client_filter = $this->db->placehold("AND o.client_status = 'nk'");
                break;
                case 'repeat':
                    $client_filter = $this->db->placehold("AND o.client_status = 'rep'");
                break;
                case 'pk':
                    $client_filter = $this->db->placehold("AND (o.client_status = 'pk' OR o.client_status = 'crm')");
                break;
            endswitch;
        }
        
        if (!empty($filter['current']))
            $current_filter = $this->db->placehold("AND (o.manager_id = ? OR (o.manager_id IS NULL AND o.status = 0))", (int)$filter['current']);
        
        if (isset($filter['autoretry']))
            $autoretry_filter = $this->db->placehold("AND o.autoretry = ?", $filter['autoretry']);
        
		$query = $this->db->placehold("
            SELECT COUNT(o.id) AS count
            FROM __orders AS o
            LEFT JOIN __users AS u
            ON u.id = o.user_id
            WHERE 1
                $id_filter
                $offline_filter
                $status_filter
                $type_filter
                $date_from_filter
                $date_to_filter
                $issuance_date_from_filter
                $issuance_date_to_filter
                $search_filter
                $current_filter
                $autoretry_filter
                $keyword_filter
                $client_filter
        ");
        $this->db->query($query);
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($query);echo '</pre><hr />';
        $count = $this->db->result('count');

        return $count;
    }
    
    public function add_order($order)
    {
		$query = $this->db->placehold("
            INSERT INTO __orders SET ?%
        ", (array)$order);

        $this->db->query($query);
        $id = $this->db->insert_id();
        
        return $id;
    }
    
    public function update_order($id, $order)
    {
		$query = $this->db->placehold("
            UPDATE __orders SET ?% WHERE id = ?
        ", (array)$order, (int)$id);
        $this->db->query($query);
        
        return $id;
    }
    
    public function delete_order($id)
    {
		$query = $this->db->placehold("
            DELETE FROM __orders WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
    }
}