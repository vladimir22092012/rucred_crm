<?php

class Documents extends Core
{
    private $sudblock_create_documents_sud = array(
        'SUD_PRIKAZ',
        'SUD_SPRAVKA',
    );
    
    private $sudblock_create_documents_fssp = array(
        'SUD_VOZBUZHDENIE',
    );
    
    private $templates = array(
        'SOGLASIE_MINB' =>  'soglasie_minb.tpl',
        'SOGLASIE_NA_KRED_OTCHET' => 'soglasie_na_kred_otchet.tpl',
        'SOGLASIE_NA_OBR_PERS_DANNIH' => 'soglasie_na_obr_pers_dannih.tpl',
        'SOGLASIE_RABOTODATEL' => 'soglasie_rabotadatelu.tpl',
        'SOGLASIE_RDB' => 'soglasie_rdb.tpl',
        'SOGLASIE_RUKRED_RABOTODATEL' => 'soglasie_rukred_rabotadatel.tpl',
        'ZAYAVLENIE_NA_PERECHISL_CHASTI_ZP' => 'zayavlenie_na_perechislenie_chasti_zp.tpl',
        'ZAYAVLENIE_ZP_V_SCHET_POGASHENIYA_MKR' => 'zayavlenie_zp_v_schet_pogasheniya_mrk.tpl',
        'INDIVIDUALNIE_USLOVIA' => 'individualnie_usloviya.tpl'
    );
    
    
    private $names = array(
        'SOGLASIE_MINB' =>  'Согласие на обработку персональных данных МинБанк',
        'SOGLASIE_NA_KRED_OTCHET' => 'Согласие на предоставление кредитного отчета',
        'SOGLASIE_NA_OBR_PERS_DANNIH' => 'Согласие на обработку персональных данных',
        'SOGLASIE_RABOTODATEL' => 'Согласие на обработку персональных данных, разрешенных субъектом персональных данных для распространения',
        'SOGLASIE_RDB' => 'Согласие на обработку персональных данных РосДорБанк',
        'SOGLASIE_RUKRED_RABOTODATEL' => 'Согласие Рукред Работодателю',
        'ZAYAVLENIE_NA_PERECHISL_CHASTI_ZP' => 'Заявление на перечисление части зп в счет обслуживания микрозайма',
        'ZAYAVLENIE_ZP_V_SCHET_POGASHENIYA_MKR' => 'Заявление о перечислении части заработной платы на счёт третьего лица',
        'INDIVIDUALNIE_USLOVIA' => 'Индивидуальные условия микрозайма'
    );
    
    private $client_visible = array(
        'ANKETA_PEP' => 1,
        'SOGLASIE_VZAIMODEYSTVIE' => 0,
        'SOGLASIE_MEGAFON' => 0,
        'SOGLASIE_SCORING' => 0,
        'SOGLASIE_SPISANIE' => 0,
        'PRICHINA_OTKAZA' => 0,
        'INDIVIDUALNIE_USLOVIA' => 1,

        'DOP_SOGLASHENIE_K_TRUDOVOMU_DOGOVORU' => 0,
        'SOGLASIE_MINB' =>  0,
        'SOGLASIE_NA_KRED_OTCHET' => 0,
        'SOGLASIE_NA_OBR_PERS_DANNIH' => 0,
        'SOGLASIE_RABOTODATEL' => 0,
        'SOGLASIE_RDB' => 0,
        'SOGLASIE_RUKRED_RABOTODATEL' => 0,
        'ZAYAVLENIE_NA_PERECHISL_CHASTI_ZP' => 0,
        'ZAYAVLENIE_ZP_V_SCHET_POGASHENIYA_MKR' => 0,
        
        'IND_USLOVIYA_NL' => 1,
        'POLIS_STRAHOVANIYA' => 1,
        'DOP_USLUGI_VIDACHA' => 0,

        'DOP_SOGLASHENIE_PROLONGATSIYA' => 1,
        'DOP_USLUGI_PROLONGATSIYA' => 0,

        'SUD_PRIKAZ' => 0,
        'SUD_SPRAVKA' => 0,
        'SUD_VOZBUZHDENIE' => 0,

        'OFFLINE_AKT_CONSULTATION' => 0,
        'OFFLINE_ANKETA' => 0,
        'OFFLINE_DOGOVOR' => 0,
        'OFFLINE_DOGOVOR_CONSULTATION' => 0,
        'OFFLINE_DOP_SOGLASHENIE' => 0,
        'OFFLINE_PKO' => 0,
        'OFFLINE_RKO' => 0,
        'OFFLINE_ASP' => 0,
        'OFFLINE_OBRABOTKA' => 0,
        'OFFLINE_INFORM' => 0,
        'OFFLINE_SMS' => 0,
    );
    
    public function create_offline_documents($contract_id)
    {
    	if ($contract = $this->contracts->get_contract((int)$contract_id))
        {
            $contract->order = $this->orders->get_order($contract->order_id);
            $contract->user = $this->users->get_user((int)$contract->user_id);
            
            $types = array(
                'OFFLINE_AKT_CONSULTATION',
                'OFFLINE_ANKETA',
                'OFFLINE_DOGOVOR',
                'OFFLINE_DOGOVOR_CONSULTATION',
                'OFFLINE_DOP_SOGLASHENIE',
                'OFFLINE_PKO',
                'OFFLINE_RKO',
                'OFFLINE_ASP',
                'OFFLINE_OBRABOTKA',
                'OFFLINE_INFORM',
                'OFFLINE_SMS',            
            );
            
            foreach ($types as $t)
                $this->create_document(array(
                    'type' => $t,
                    'user_id' => $contract->user_id,
                    'order_id' => $contract->order_id,
                    'contract_id' => $contract->id,
                    'params' => $contract
                ));
        }
    }
    
    
    public function get_sudblock_create_documents($block)
    {
        if ($block == 'sud')
        	return $this->sudblock_create_documents_sud;
        if ($block == 'fssp')
        	return $this->sudblock_create_documents_fssp;
    }
    
    
    public function create_document($data)
    {
        $created = array(
            'user_id' => isset($data['user_id']) ? $data['user_id'] : 0,
            'order_id' => isset($data['order_id']) ? $data['order_id'] : 0,
            'contract_id' => isset($data['contract_id']) ? $data['contract_id'] : 0,
            'type' => $data['type'],
            'name' => $this->names[$data['type']],
            'template' => $this->templates[$data['type']],
            'client_visible' => $this->client_visible[$data['type']],
            'params' => $data['params'],
            'created' => date('Y-m-d H:i:s'),
        );
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($created);echo '</pre><hr />';
        $id =  $this->add_document($created);
        
        return $id;
    }
    
    public function get_templates()
    {
    	return $this->templates;
    }    
    
    public function get_template($type)
    {
    	return isset($this->templates[$type]) ? $this->templates[$type] : null;
    }    
    
    public function get_contract_document($contract_id, $type)
    {
		$query = $this->db->placehold("
            SELECT * 
            FROM __documents
            WHERE contract_id = ?
            AND type = ?
        ", (int)$contract_id, (string)$type);
        $this->db->query($query);
        if ($result = $this->db->result())
            $result->params = unserialize($result->params);

        return $result;
    }
    
    
	public function get_document($id)
	{
		$query = $this->db->placehold("
            SELECT * 
            FROM __documents
            WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
        if ($result = $this->db->result())
            $result->params = unserialize($result->params);

        return $result;
    }
    
	public function get_documents($filter = array())
	{
		$id_filter = '';
		$user_id_filter = '';
		$order_id_filter = '';
		$contract_id_filter = '';
		$client_visible_filter = '';
		$type_filter = '';
        $keyword_filter = '';
        $limit = 1000;
		$page = 1;
        
        if (!empty($filter['id']))
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));
        
        if (!empty($filter['user_id']))
            $user_id_filter = $this->db->placehold("AND user_id IN (?@)", array_map('intval', (array)$filter['user_id']));
        
        if (!empty($filter['order_id']))
            $order_id_filter = $this->db->placehold("AND order_id IN (?@)", array_map('intval', (array)$filter['order_id']));
        
        if (!empty($filter['contract_id']))
            $contract_id_filter = $this->db->placehold("AND contract_id IN (?@)", array_map('intval', (array)$filter['contract_id']));
        
        if (isset($filter['client_visible']))
            $client_visible_filter = $this->db->placehold("AND client_visible = ?", (int)$filter['client_visible']);
        
        if (isset($filter['type']))
            $type_filter = $this->db->placehold("AND type = ?", (string)$filter['type']);
        
		if(isset($filter['keyword']))
		{
			$keywords = explode(' ', $filter['keyword']);
			foreach($keywords as $keyword)
				$keyword_filter .= $this->db->placehold('AND (name LIKE "%'.$this->db->escape(trim($keyword)).'%" )');
		}
        
		if(isset($filter['limit']))
			$limit = max(1, intval($filter['limit']));

		if(isset($filter['page']))
			$page = max(1, intval($filter['page']));
            
        $sql_limit = $this->db->placehold(' LIMIT ?, ? ', ($page-1)*$limit, $limit);

        $query = $this->db->placehold("
            SELECT * 
            FROM __documents
            WHERE 1
                $id_filter
        		$user_id_filter
        		$order_id_filter
        		$contract_id_filter
                $client_visible_filter
                $type_filter
 	            $keyword_filter
            ORDER BY id ASC 
            $sql_limit
        ");
        $this->db->query($query);
        if ($results = $this->db->results())
        {
            foreach ($results as $result)
            {
                $result->params = unserialize($result->params);
            }
        }
        
        return $results;
	}
    
	public function count_documents($filter = array())
	{
        $id_filter = '';
		$user_id_filter = '';
		$order_id_filter = '';
		$contract_id_filter = '';
        $client_visible_filter = '';
        $keyword_filter = '';
        
        if (!empty($filter['id']))
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));
		
        if (!empty($filter['user_id']))
            $user_id_filter = $this->db->placehold("AND user_id IN (?@)", array_map('intval', (array)$filter['user_id']));
        
        if (!empty($filter['order_id']))
            $order_id_filter = $this->db->placehold("AND order_id IN (?@)", array_map('intval', (array)$filter['order_id']));
        
        if (!empty($filter['contract_id']))
            $contract_id_filter = $this->db->placehold("AND contract_id IN (?@)", array_map('intval', (array)$filter['contract_id']));
        
        if (isset($filter['client_visible']))
            $client_visible_filter = $this->db->placehold("AND client_visible = ?", (int)$filter['client_visible']);
        
        if(isset($filter['keyword']))
		{
			$keywords = explode(' ', $filter['keyword']);
			foreach($keywords as $keyword)
				$keyword_filter .= $this->db->placehold('AND (name LIKE "%'.$this->db->escape(trim($keyword)).'%" )');
		}
                
		$query = $this->db->placehold("
            SELECT COUNT(id) AS count
            FROM __documents
            WHERE 1
                $id_filter
        		$user_id_filter
        		$order_id_filter
        		$contract_id_filter
                $client_visible_filter
                $keyword_filter
        ");
        $this->db->query($query);
        $count = $this->db->result('count');
	
        return $count;
    }
    
    public function add_document($document)
    {
        $document = (array)$document;
        
        if (isset($document['params']))
            $document['params'] = serialize($document['params']);
        
		$query = $this->db->placehold("
            INSERT INTO __documents SET ?%
        ", $document);
        $this->db->query($query);

        $id = $this->db->insert_id();
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($query);echo '</pre><hr />';exit;
        return $id;
    }
    
    public function update_document($id, $document)
    {
        $document = (array)$document;
        
        if (isset($document['params']))
            $document['params'] = serialize($document['params']);
        
		$query = $this->db->placehold("
            UPDATE __documents SET ?% WHERE id = ?
        ", $document, (int)$id);
        $this->db->query($query);
        
        return $id;
    }
    
    public function delete_document($id)
    {
		$query = $this->db->placehold("
            DELETE FROM __documents WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
    }

    public function delete_documents($id)
    {
        $query = $this->db->placehold("
            DELETE FROM __documents WHERE order_id = ?
        ", (int)$id);
        $this->db->query($query);
    }
    
    
    public function create_contract_document($document_type, $contract)
    {
        $ob_date = new DateTime();
        $ob_date->add(DateInterval::createFromDateString($contract->period.' days'));
        $return_date = $ob_date->format('Y-m-d H:i:s');

        $return_amount = round($contract->amount + $contract->amount * $contract->base_percent * $contract->period / 100, 2);
        $return_amount_rouble = (int)$return_amount;
        $return_amount_kop = ($return_amount - $return_amount_rouble) * 100;

        $contract_order = $this->orders->get_order((int)$contract->order_id);
        
        $params = array(
            'lastname' => $contract_order->lastname,
            'firstname' => $contract_order->firstname,
            'patronymic' => $contract_order->patronymic,
            'phone' => $contract_order->phone_mobile,
            'birth' => $contract_order->birth,
            'number' => $contract->number,
            'contract_date' => date('Y-m-d H:i:s'),
            'return_date' => $return_date,
            'return_date_day' => date('d', strtotime($return_date)),
            'return_date_month' => date('m', strtotime($return_date)),
            'return_date_year' => date('Y', strtotime($return_date)),
            'return_amount' => $return_amount,
            'return_amount_rouble' => $return_amount_rouble,
            'return_amount_kop' => $return_amount_kop,
            'base_percent' => $contract->base_percent,
            'amount' => $contract->amount,
            'period' => $contract->period,
            'return_amount_percents' => round($contract->amount * $contract->base_percent * $contract->period / 100, 2),
            'passport_serial' => $contract_order->passport_serial,
            'passport_date' => $contract_order->passport_date,
            'subdivision_code' => $contract_order->subdivision_code,
            'passport_issued' => $contract_order->passport_issued,
            'passport_series' => substr(str_replace(array(' ', '-'), '', $contract_order->passport_serial), 0, 4),
            'passport_number' => substr(str_replace(array(' ', '-'), '', $contract_order->passport_serial), 4, 6),
            'asp' => $contract->accept_code,
            'insurance_summ' => round($contract->amount * 0.15, 2),
        );
        $regaddress_full = empty($contract_order->Regindex) ? '' : $contract_order->Regindex.', ';
        $regaddress_full .= trim($contract_order->Regregion.' '.$contract_order->Regregion_shorttype);
        $regaddress_full .= empty($contract_order->Regcity) ? '' : trim(', '.$contract_order->Regcity.' '.$contract_order->Regcity_shorttype);
        $regaddress_full .= empty($contract_order->Regdistrict) ? '' : trim(', '.$contract_order->Regdistrict.' '.$contract_order->Regdistrict_shorttype);
        $regaddress_full .= empty($contract_order->Reglocality) ? '' : trim(', '.$contract_order->Reglocality.' '.$contract_order->Reglocality_shorttype);
        $regaddress_full .= empty($contract_order->Reghousing) ? '' : ', д.'.$contract_order->Reghousing;
        $regaddress_full .= empty($contract_order->Regbuilding) ? '' : ', стр.'.$contract_order->Regbuilding;
        $regaddress_full .= empty($contract_order->Regroom) ? '' : ', к.'.$contract_order->Regroom;

        $params['regaddress_full'] = $regaddress_full;

        if (!empty($contract->insurance_id))
        {
            $params['insurance'] = $this->insurances->get_insurance($contract->insurance_id);
        }
        

        $this->documents->create_document(array(
            'user_id' => $contract->user_id,
            'order_id' => $contract->order_id,
            'contract_id' => $contract->id,
            'type' => $document_type,
            'params' => $params,                
        ));

    }
    
}