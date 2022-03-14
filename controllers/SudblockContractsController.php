<?php

class SudblockContractsController extends Controller
{
    public function fetch()
    {
        $items_per_page = 50;

    	if ($this->request->method('post'))
        {
            switch ($this->request->post('action', 'string')):
            
                case 'manager':
                    $this->set_manager_action();
                break;
                
                case 'status':
                    $this->set_status_action();
                break;
                
                case 'ready_document':
                    $this->ready_document_action();
                break;

                case 'workout':
                    $this->set_workout_action();
                break;
                



                case 'contactperson_comment':
                    $this->contactperson_comment_action();
                break;
                
                case 'sud_label':
                    $this->set_sud_label_action();
                break;
                
                case 'send_sms':
                    $this->send_sms_action();
                break;
                
            endswitch;
        }
        else
        {
            if ($this->request->get('download'))
            {
                $this->download_action();
            }
        }
        
        $order_ids = array();
    	$user_ids = array();
        $contracts = array();
        
        $filter = array();
        
        $sms_templates = $this->sms->get_templates(array('type' => 'collection'));
        $this->design->assign('sms_templates', $sms_templates);
        
        if (!($period = $this->request->get('period')))
            $period = 'all';

        switch ($period):
             
             case 'month':
                $filter['inssuance_date_from'] = date('Y-m-01');                
             break;
             
             case 'year':
                $filter['inssuance_date_from'] = date('Y-01-01');
             break;
             
             case 'all':
                $filter['inssuance_date_from'] = null;
                $filter['inssuance_date_to'] = null;
             break;
             
             case 'optional':
                $daterange = $this->request->get('daterange');
                $filter_daterange = array_map('trim', explode('-', $daterange));
                $filter['inssuance_date_from'] = date('Y-m-d', strtotime($filter_daterange[0]));
                $filter['inssuance_date_to'] = date('Y-m-d', strtotime($filter_daterange[1]));                
             break;
             
        endswitch;
        $this->design->assign('period', $period);
        
        if ($search = $this->request->get('search'))
        {
            $filter['search'] = array_filter($search);
            $this->design->assign('search', array_filter($search));
        }
        
        
        if ($this->manager->role == 'exactor' || $this->manager->role == 'sudblock')
        {
            $filter['manager_id'] = $this->manager->id;
        }
        elseif ($manager_id = $this->request->get('manager_id'))
        {
            $filter['manager_id'] = $manager_id;
        }
        
        if ($status = $this->request->get('status'))
        {
            $filter['status'] = $status;
        }
        
        if (!($sort = $this->request->get('sort')))
        {
            $sort = 'manager_id_asc';
        }
        $this->design->assign('sort', $sort);
        $filter['sort'] = $sort;
        
        $filter['sort_workout'] = 1;
        
        if ($page_count = $this->request->get('page_count'))
        {
            setcookie('page_count', $page_count, time()+86400*30, '/');
            if ($page_count == 'all')
                $items_per_page = 10000;
            else
                $items_per_page = $page_count;
                
            $this->design->assign('page_count', $page_count);
        }
        elseif (!empty($_COOKIE['page_count']))
        {
            if ($_COOKIE['page_count'] == 'all')
                $items_per_page = 10000;
            else
                $items_per_page = $_COOKIE['page_count'];            
        
            $this->design->assign('page_count', $_COOKIE['page_count']);
        }


        $current_page = $this->request->get('page', 'integer');
		$current_page = max(1, $current_page);
		$this->design->assign('current_page_num', $current_page);
		$this->design->assign('items_per_page', $items_per_page);

		$contracts_count = $this->sudblock->count_contracts($filter);
		
		$pages_num = ceil($contracts_count/$items_per_page);
		$this->design->assign('total_pages_num', $pages_num);
		$this->design->assign('total_orders_count', $contracts_count);

		$filter['page'] = $current_page;
		$filter['limit'] = $items_per_page;
        
        $filter['sort_workout'] = 1;
        
        foreach ($this->sudblock->get_contracts($filter) as $con)
        {
            $date1 = new DateTime(date('Y-m-d', strtotime($con->created)));
            $date2 = new DateTime(date('Y-m-d'));
                
            $diff = $date2->diff($date1);
            $con->delay = $diff->days;
                                    
            $contracts[$con->id] = $con;
        }

        if (!empty($contracts))
        {
            
            
            
            $this->design->assign('contracts', $contracts);
            
        }
        
        $statuses = array();
        foreach ($this->sudblock->get_statuses() as $st)
            $statuses[$st->id] = $st;
        $this->design->assign('statuses', $statuses);
        
        
        return $this->design->fetch('sudblock_contracts.tpl');
    }
    


    private function set_manager_action()
    {
        $contract_id = $this->request->post('contract_id', 'integer');
        $manager_id = $this->request->post('manager_id', 'integer');
        
        $this->sudblock->update_contract($contract_id, array('manager_id' => $manager_id));

        $this->json_output(array('success' => 1));
        exit;
    }
    
    private function set_status_action()
    {
        $contract_id = $this->request->post('contract_id', 'integer');
        $status_id = $this->request->post('status_id', 'integer');
        
        $this->sudblock->update_contract($contract_id, array('status' => $status_id));

        $this->json_output(array('success' => 1));
        exit;
    }
    
    public function ready_document_action()
    {
        $ready = $this->request->post('ready', 'integer');
        $document_id = $this->request->post('document_id', 'integer');
        
        $this->sudblock->update_document($document_id, array('ready' => $ready));

        $this->json_output(array(
            'success' => 1,
            'ready' => $ready
        ));
        exit;
        
    }

    private function set_workout_action()
    {
        $contract_id = $this->request->post('contract_id', 'integer');
        $workout = $this->request->post('workout', 'integer');
    
        $res = $this->sudblock->update_contract($contract_id, array('workout'=>$workout));

        $this->json_output(array('success' => $res));
        exit;
    }
    
    
    
    
    
    
    
    private function set_sud_label_action()
    {
        $sud = $this->request->post('sud', 'integer');
        $contract_id = $this->request->post('contract_id', 'integer');
        
        $old_contract = $this->contracts->get_contract((int)$contract_id);

        $this->contracts->update_contract($contract_id, array('sud' => $sud));

        $this->changelogs->add_changelog(array(
            'manager_id' => $this->manager->id,
            'created' => date('Y-m-d H:i:s'),
            'type' => 'collection_status',
            'old_values' => serialize(array(
                'sud' => $old_contract->sud,
            )),
            'new_values' => serialize(array(
                'sud' => $sud,
            )),
            'order_id' => $old_contract->order_id,
            'user_id' => $old_contract->user_id,
        ));

        $this->json_output(array('success' => 1));
        exit;
    }
    
    private function contactperson_comment_action()
    {
        $comment = trim($this->request->post('text'));
        $contactperson_id = $this->request->post('contactperson_id', 'integer');
        $order_id = $this->request->post('order_id', 'integer');
        
        if ($contactperson = $this->contactpersons->get_contactperson($contactperson_id))
        {
            if (!empty($comment))
            {
                $this->contactpersons->update_contactperson($contactperson_id, array('comment' => $comment));
                $this->comments->add_comment(array(
                    'order_id' => $order_id,
                    'user_id' => $contactperson->user_id,
                    'contactperson_id' => $contactperson_id,
                    'manager_id' => $this->manager->id,
                    'text' => $comment,
                    'created' => date('Y-m-d H:i:s'),
                    'sent' => 0,
                    'status' => 0,
                ));
                $this->json_output(array('success' => 1));
            }
            else
            {
                $this->json_output(array('error' => 'Напишите комментарий'));
                
            }
        }
        else
        {
            $this->json_output(array('error' => 'Контакное лицо не найдено'));
        }
        exit;
    }
    
    private function send_sms_action()
    {
        $yuk = $this->request->post('yuk', 'integer');
        $user_id = $this->request->post('user_id', 'integer');
        $template_id = $this->request->post('template_id', 'integer');
        
        $user = $this->users->get_user((int)$user_id);   

        $template = $this->sms->get_template($template_id);
        
        $resp = $this->sms->send(
            /*'79276928586'*/$user->phone_mobile, 
            $template->template
        );
        
        $this->sms->add_message(array(
            'user_id' => $user->id,
            'order_id' => 0,
            'phone' => $user->phone_mobile,
            'message' => $template->template,
            'created' => date('Y-m-d H:i:s'),
        ));
        
        $this->changelogs->add_changelog(array(
            'manager_id' => $this->manager->id,
            'created' => date('Y-m-d H:i:s'),
            'type' => 'send_sms',
            'old_values' => '',
            'new_values' => $template->template,
            'user_id' => $user->id,
        ));
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';		
        $this->json_output(array('success'=>true));
    }

    private function download_action()
    {
        $managers = array();
        foreach ($this->managers->get_managers() as $m)
            $managers[$m->id] = $m;

        $filename = 'files/reports/sudblock.xls';
        require $this->config->root_dir.'PHPExcel/Classes/PHPExcel.php';

        $contracts = array();
        $user_ids = array();
        $first_contract_ids = array();
        foreach ($this->sudblock->get_contracts() as $c)
        {
            $user_ids[] = $c->user_id;
            $contracts[$c->id] = $c;
            $first_contract_ids[] = $c->contract_id;
        }
        
        if (!empty($user_ids))
        {
            $users = array();
            foreach ($this->users->get_users(array('id'=>$user_ids)) as $u)
                $users[$u->id] = $u;
        }
        
        if (!empty($first_contract_ids))
        {
            $first_contracts = array();
            foreach ($this->contracts->get_contracts(array('id'=>$first_contract_ids)) as $fc)
                $first_contracts[$fc->id] = $fc;
        }

        foreach ($contracts as $contract)
        {
            if (isset($users[$contract->user_id]))
                $contract->user = $users[$contract->user_id];
            if (isset($first_contracts[$contract->contract_id]))
                $contract->first_contract = $first_contracts[$contract->contract_id];
        }

        $statuses = array();
        foreach ($this->sudblock->get_statuses() as $st)
            $statuses[$st->id] = $st;


        $excel = new PHPExcel();
        
        $excel->setActiveSheetIndex(0);
        $active_sheet = $excel->getActiveSheet();
        
        $active_sheet->setTitle("Судебные договора");
    
        $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(9);
        $excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        
        $active_sheet->getColumnDimension('A')->setWidth(7);
        $active_sheet->getColumnDimension('B')->setWidth(15);
        $active_sheet->getColumnDimension('C')->setWidth(35);
        $active_sheet->getColumnDimension('D')->setWidth(15);
        $active_sheet->getColumnDimension('E')->setWidth(25);
        $active_sheet->getColumnDimension('F')->setWidth(20);
        $active_sheet->getColumnDimension('G')->setWidth(20);
        $active_sheet->getColumnDimension('H')->setWidth(35);
        $active_sheet->getColumnDimension('I')->setWidth(10);
        $active_sheet->getColumnDimension('J')->setWidth(10);
        $active_sheet->getColumnDimension('K')->setWidth(20);
        $active_sheet->getColumnDimension('L')->setWidth(35);
        $active_sheet->getColumnDimension('M')->setWidth(15);
        $active_sheet->getColumnDimension('N')->setWidth(15);
        $active_sheet->getColumnDimension('O')->setWidth(15);
        $active_sheet->getColumnDimension('P')->setWidth(20);
        $active_sheet->getColumnDimension('Q')->setWidth(30);
        $active_sheet->getColumnDimension('R')->setWidth(10);
        $active_sheet->getColumnDimension('S')->setWidth(10);
        $active_sheet->getColumnDimension('T')->setWidth(15);

        $active_sheet->setCellValue('A1', 'Орг');
        $active_sheet->setCellValue('B1', 'Дата действия');
        $active_sheet->setCellValue('C1', 'Договор');
        $active_sheet->setCellValue('D1', 'Номер договора');
        $active_sheet->setCellValue('E1', 'Цессия');
        $active_sheet->setCellValue('F1', 'Территориальный орган ФССП');//---
        $active_sheet->setCellValue('G1', 'Дата возбуждения ИП');
        $active_sheet->setCellValue('H1', 'ФИОКлиента');//---
        $active_sheet->setCellValue('I1', 'Дни в работе');//---
        $active_sheet->setCellValue('J1', 'Количество дней без оплат');//---
        $active_sheet->setCellValue('K1', 'Регион регистрации');//---
        $active_sheet->setCellValue('L1', 'Суд');//---
        $active_sheet->setCellValue('M1', 'Дата договора');//---
        $active_sheet->setCellValue('N1', 'Сумма договора');//---
        $active_sheet->setCellValue('O1', 'Ответственный');//---
        $active_sheet->setCellValue('P1', 'Поставщик займа');//---
        $active_sheet->setCellValue('Q1', 'Работа');//---
        $active_sheet->setCellValue('R1', 'Сумма долга');//---
        $active_sheet->setCellValue('S1', 'Количество договоров');//---
        $active_sheet->setCellValue('T1', 'Событие');//---

        
        $i = 2;
        foreach ($contracts as $contract)
        {
            $date1 = new DateTime(date('Y-m-d', strtotime($contract->created)));
            $date2 = new DateTime(date('Y-m-d'));    
            $diff = $date2->diff($date1);
            $contract->delay = $diff->days;
            
            //TODO: дата последней оплаты
            if ($pay_operations = $this->operations->get_operations(array('contract_id'=>$contract->contract_id, 'type'=>'PAY')))
            {
                $date_pay_operations = NULL;
                foreach ($pay_operations as $po)
                {
                    if (empty($date_pay_operations) || strtotime($po->created) > strtotime($date_pay_operations))
                        $date_pay_operations = $po->created;
                }
                if (!empty($date_pay_operations))
                {
                    $date1 = new DateTime(date('Y-m-d', strtotime($date_pay_operations)));
                    $date2 = new DateTime(date('Y-m-d'));    
                    $diff = $date2->diff($date1);
                    $contract->last_payment_delay = $diff->days;
                }
            }
            
            $text_cessions = '';
            if (!empty($contract->cession_info))
                foreach ($contract->cession_info as $ci)
                {
                    $text_cessions .= $ci->number;
                    if (!empty($ci->date))
                        $text_cessions .= ' от '.$ci->date.' ';
                }
            $contract_status = '';
            
            $active_sheet->setCellValue('A'.$i, '');// Орг
            $active_sheet->setCellValue('B'.$i, '');// Дата действия
            if ($contract->status < 2)
                $active_sheet->setCellValue('C'.$i, 'Займ выдача '.$contract->first_number.' от '.$contract->first_contract->inssuance_date);
            else
                $active_sheet->setCellValue('C'.$i, 'Судебный Договор '.$contract->id.' от '.$contract->created);
            $active_sheet->setCellValue('D'.$i, $contract->first_number);
            $active_sheet->setCellValue('E'.$i, $text_cessions);
            $active_sheet->setCellValue('F'.$i, '');//---Территориальный орган ФССП
            $active_sheet->setCellValue('G'.$i, '');//Дата возбуждения ИП
            $active_sheet->setCellValue('H'.$i, $contract->lastname.' '.$contract->firstname.' '.$contract->patronymic);//---
            $active_sheet->setCellValue('I'.$i, $contract->delay);//---
            $active_sheet->setCellValue('J'.$i, empty($contract->last_payment_delay) ? '' : $contract->last_payment_delay);//---
            $active_sheet->setCellValue('K'.$i, $contract->region);//---
            $active_sheet->setCellValue('L'.$i, $contract->tribunal);//---
            $active_sheet->setCellValue('M'.$i, $contract->first_contract->inssuance_date);//---'Дата договора'
            $active_sheet->setCellValue('N'.$i, $contract->first_contract->amount);//---'Сумма договора'
            $active_sheet->setCellValue('O'.$i, empty($contract->manager_id) ? '' : $managers[$contract->manager_id]->name);//---
            $active_sheet->setCellValue('P'.$i, $contract->provider);//---
            $active_sheet->setCellValue('Q'.$i, '');//---Работа
            $active_sheet->setCellValue('R'.$i, '');//---Сумма долга
            $active_sheet->setCellValue('S'.$i, '1');//---Количество договоров
            $active_sheet->setCellValue('T'.$i, $statuses[$contract->status]->name);//---Статус

            
            $i++;
        }
        
        $objWriter = PHPExcel_IOFactory::createWriter($excel,'Excel5');

        $objWriter->save($this->config->root_dir.$filename);
        
        header('Location:'.$this->config->root_url.'/'.$filename);
        exit;
    }
}