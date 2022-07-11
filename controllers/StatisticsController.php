<?php

ini_set('max_execution_time', 40);

class StatisticsController extends Controller
{
    public function fetch()
    {

        switch ($this->request->get('action', 'string')) :
            case 'main':
                return $this->action_main();
            break;

            case 'report':
                return $this->action_report();
            break;

            case 'conversion':
                return $this->action_conversion();
            break;

            case 'expired':
                return $this->action_expired();
            break;

            case 'free_pk':
                return $this->action_free_pk();
            break;

            case 'scorista_rejects':
                return $this->action_scorista_rejects();
            break;

            case 'contracts':
                return $this->action_contracts();
            break;

            case 'payments':
                return $this->action_payments();
            break;

            case 'eventlogs':
                return $this->action_eventlogs();
            break;

            case 'penalties':
                return $this->action_penalties();
            break;

            default:
                return false;
        endswitch;
    }

    private function action_main()
    {
        return $this->design->fetch('statistics/main.tpl');
    }

    private function action_report()
    {
        $this->statistics->get_operative_report('2021-05-01', '2021-05-30');

        return $this->design->fetch('statistics/report.tpl');
    }

    private function action_conversion()
    {
        return $this->design->fetch('statistics/conversion.tpl');
    }

    private function action_expired()
    {
        return $this->design->fetch('statistics/expired.tpl');
    }

    private function action_free_pk()
    {
        return $this->design->fetch('statistics/free_pk.tpl');
    }

    private function action_scorista_rejects()
    {
        $reasons = array();
        foreach ($this->reasons->get_reasons() as $reason) {
            $reasons[$reason->id] = $reason;
        }
        $this->design->assign('reasons', $reasons);


        if ($daterange = $this->request->get('daterange')) {
            list($from, $to) = explode('-', $daterange);

            $date_from = date('Y-m-d', strtotime($from));
            $date_to = date('Y-m-d', strtotime($to));

            $this->design->assign('date_from', $date_from);
            $this->design->assign('date_to', $date_to);
            $this->design->assign('from', $from);
            $this->design->assign('to', $to);

            $query_reason = '';
            if ($filter_reason = $this->request->get('reason_id')) {
                if ($filter_reason != 'all') {
                    $query_reason = $this->db->placehold("AND o.reason_id = ?", (int)$filter_reason);
                }

                $this->design->assign('filter_reason', $filter_reason);
            }

            $query = $this->db->placehold("
                SELECT
                    o.id AS order_id,
                    o.date,
                    o.reason_id,
                    o.reject_reason,
                    o.user_id,
                    o.manager_id,
                    u.lastname,
                    u.firstname,
                    u.patronymic,
                    u.phone_mobile,
                    u.email
                FROM __orders AS o
                LEFT JOIN __users AS u
                ON u.id = o.user_id
                WHERE o.status IN (3, 8)
                $query_reason
                AND DATE(o.date) >= ?
                AND DATE(o.date) <= ?
                GROUP BY order_id
            ", $date_from, $date_to);
            $this->db->query($query);

            $orders = array();
            foreach ($this->db->results() as $o) {
                $orders[$o->order_id] = $o;
            }

            if (!empty($orders)) {
                if ($scorings = $this->scorings->get_scorings(array('order_id' => array_keys($orders), 'type' => 'scorista'))) {
                    foreach ($scorings as $scoring) {
                        $orders[$scoring->order_id]->scoring = $scoring;
                    }
                }
            }


            switch ($this->request->get('scoring')) :
                case '499-':
                    foreach ($orders as $key => $order) {
                        if (empty($order->scoring->scorista_ball) || $order->scoring->scorista_ball > 499) {
                            unset($orders[$key]);
                        }
                    }
                    break;

                case '500-549':
                    foreach ($orders as $key => $order) {
                        if (empty($order->scoring->scorista_ball) || $order->scoring->scorista_ball < 500 || $order->scoring->scorista_ball > 549) {
                            unset($orders[$key]);
                        }
                    }
                    break;

                case '550+':
                    foreach ($orders as $key => $order) {
                        if (empty($order->scoring->scorista_ball) || $order->scoring->scorista_ball < 550) {
                            unset($orders[$key]);
                        }
                    }
                    break;
            endswitch;
            $this->design->assign('filter_scoring', $this->request->get('scoring'));


            if ($this->request->get('download') == 'excel') {
                $managers = array();
                foreach ($this->managers->get_managers() as $m) {
                    $managers[$m->id] = $m;
                }

                //фикс расхожения данных в документе и на сайте
                $fix_managers = [];
                foreach ($managers as $manager) {
                    $fix_managers[$manager->id] = $manager;
                }

                $filename = 'files/reports/orders.xls';
                require $this->config->root_dir.'PHPExcel/Classes/PHPExcel.php';

                $excel = new PHPExcel();

                $excel->setActiveSheetIndex(0);
                $active_sheet = $excel->getActiveSheet();

                $active_sheet->setTitle("Выдачи ".$from."-".$to);

                $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);
                $excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                $active_sheet->getColumnDimension('A')->setWidth(15);
                $active_sheet->getColumnDimension('B')->setWidth(15);
                $active_sheet->getColumnDimension('C')->setWidth(45);
                $active_sheet->getColumnDimension('D')->setWidth(20);
                $active_sheet->getColumnDimension('E')->setWidth(20);
                $active_sheet->getColumnDimension('F')->setWidth(10);
                $active_sheet->getColumnDimension('G')->setWidth(10);
                $active_sheet->getColumnDimension('H')->setWidth(30);

                $active_sheet->setCellValue('A1', 'Дата');
                $active_sheet->setCellValue('B1', 'Заявка');
                $active_sheet->setCellValue('C1', 'ФИО');
                $active_sheet->setCellValue('D1', 'Телефон');
                $active_sheet->setCellValue('E1', 'Email');
                $active_sheet->setCellValue('F1', 'Менеджер');//---
                $active_sheet->setCellValue('G1', 'Причина');
                $active_sheet->setCellValue('H1', 'Скориста');//---

                $i = 2;
                foreach ($orders as $contract) {
                    $active_sheet->setCellValue('A'.$i, date('d.m.Y', strtotime($contract->date)));
                    $active_sheet->setCellValue('B'.$i, $contract->order_id);
                    $active_sheet->setCellValue('C'.$i, $contract->lastname.' '.$contract->firstname.' '.$contract->patronymic);
                    $active_sheet->setCellValue('D'.$i, $contract->phone_mobile);
                    $active_sheet->setCellValue('E'.$i, $contract->email);
                    $active_sheet->setCellValue('F'.$i, $fix_managers[$contract->manager_id]->name);
                    $active_sheet->setCellValue('G'.$i, ($contract->reason_id ? $reasons[$contract->reason_id]->admin_name : $contract->reject_reason));
                    $active_sheet->setCellValue('H'.$i, isset($contract->scoring) ? $contract->scoring->scorista_ball : '');


                    $i++;
                }

                $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');

                $objWriter->save($this->config->root_dir.$filename);

                header('Location:'.$this->config->root_url.'/'.$filename);
                exit;
            }



            $this->design->assign('orders', $orders);
        }

        return $this->design->fetch('statistics/scorista_rejects.tpl');
    }

    private function action_contracts()
    {
        if ($daterange = $this->request->get('daterange')) {
            list($from, $to) = explode('-', $daterange);

            $date_from = date('Y-m-d', strtotime($from));
            $date_to = date('Y-m-d', strtotime($to));

            $this->design->assign('date_from', $date_from);
            $this->design->assign('date_to', $date_to);
            $this->design->assign('from', $from);
            $this->design->assign('to', $to);

// сделайте выгрузку в эксель, пожалуйста, по всем выданным займам:
// дата - номер договора - ФИО+ДР - сумма - ПК/НК.
            $query = $this->db->placehold("
                SELECT
                    c.id AS contract_id,
                    c.order_id AS order_id,
                    c.number,
                    c.inssuance_date AS date,
                    c.return_date,
                    c.close_date,
                    c.amount,
                    c.user_id,
                    c.status,
                    c.collection_status,
                    c.sold,
                    o.client_status,
                    o.date AS order_date,
                    o.manager_id,
                    u.lastname,
                    u.firstname,
                    u.patronymic,
                    u.phone_mobile,
                    u.email,
                    u.birth,
                    u.UID AS uid
                FROM __contracts AS c
                LEFT JOIN __users AS u
                ON u.id = c.user_id
                LEFT JOIN __orders AS o
                ON o.id = c.order_id
                WHERE c.status IN (2, 3, 4, 7)
                AND c.type = 'base'
                AND DATE(c.inssuance_date) >= ?
                AND DATE(c.inssuance_date) <= ?
                ORDER BY contract_id
            ", $date_from, $date_to);
            $this->db->query($query);

            $contracts = array();
            foreach ($this->db->results() as $c) {
                $c->collections = array();
                $c->operations = array();
                $c->total_paid = 0;
                $contracts[$c->contract_id] = $c;
            }

            if (!empty($contracts)) {
                foreach ($this->operations->get_operations(array('contract_id'=>array_keys($contracts), 'type'=>'PAY')) as $op) {
                    $contracts[$op->contract_id]->operations[] = $op;
                    $contracts[$op->contract_id]->total_paid += $op->amount;
                }

                foreach ($this->collections->get_collections(array('contract_id'=>array_keys($contracts))) as $col) {
                    $contracts[$op->contract_id]->collections[] = $col;
                }
            }

            foreach ($contracts as $c) {
                $c->expiration = 0;

                if ($c->status == 3) {
                    if (strtotime($c->close_date) > strtotime($c->return_date)) {
                        $datetime1 = date_create(date('Y-m-d 00:00:00', strtotime($c->close_date)));
                        $datetime2 = date_create(date('Y-m-d 00:00:00', strtotime($c->return_date)));
                        $interval = date_diff($datetime1, $datetime2);
    //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($c->close_date, $c->return_date, $interval);echo '</pre><hr />';
                        $c->expiration = $interval->days;
                    }
                } else {
                    if (strtotime(date('Y-m-d H:i:s')) > strtotime($c->return_date)) {
                        $datetime1 = date_create(date('Y-m-d 00:00:00'));
                        $datetime2 = date_create(date('Y-m-d 00:00:00', strtotime($c->return_date)));
                        $interval = date_diff($datetime1, $datetime2);
    //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($c->close_date, $c->return_date, $interval);echo '</pre><hr />';
                        $c->expiration = $interval->days;
                    }
                }

                if (empty($c->client_status)) {
                    $client_contracts = $this->contracts->get_contracts(array(
                        'user_id' => $c->user_id,
                        'status' => 3,
                        'close_date_to' => $c->date
                    ));
                    if (!empty($client_contracts)) {
                        $this->orders->update_order($c->order_id, array('client_status' => 'crm'));
                    } else {
                        $loan_history = $this->soap1c->get_client_credits($c->uid);
                        if (!empty($loan_history)) {
                            $have_close_loans = 0;
                            foreach ($loan_history as $lh) {
                                if (!empty($lh->ДатаЗакрытия)) {
                                    if (strtotime($lh->ДатаЗакрытия) < strtotime($c->date)) {
                                        $have_close_loans = 1;
                                        $this->orders->update_order($c->order_id, array('client_status' => 'pk'));
                                    }
                                }
                            }
                        }

                        if (empty($have_close_loans)) {
                            $have_old_orders = 0;
                            $orders = $this->orders->get_orders(array('user_id' => $c->user_id, 'date_to' => $c->date));
                            foreach ($orders as $order) {
                                if ($order->order_id != $c->order_id) {
                                    $have_old_orders = 1;
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump('$order', $order);echo '</pre><hr />';
                                }
                            }

                            if (empty($have_old_orders)) {
                                $this->orders->update_order($c->order_id, array('client_status' => 'nk'));
                            } else {
                                $this->orders->update_order($c->order_id, array('client_status' => 'rep'));
                            }
                        }
                    }
                }
            }

            $statuses = $this->contracts->get_statuses();
            $this->design->assign('statuses', $statuses);

            $collection_statuses = $this->contracts->get_collection_statuses();
            $this->design->assign('collection_statuses', $collection_statuses);

            $managers = array();
            foreach ($this->managers->get_managers() as $m) {
                $managers[$m->id] = $m;
            }
            $this->design->assign('list_managers', $managers);

            if ($this->request->get('download') == 'excel') {
                $filename = 'files/reports/contracts.xls';
                require $this->config->root_dir.'PHPExcel/Classes/PHPExcel.php';

                $excel = new PHPExcel();

                $excel->setActiveSheetIndex(0);
                $active_sheet = $excel->getActiveSheet();

                $active_sheet->setTitle("Выдачи ".$from."-".$to);

                $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);
                $excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                $active_sheet->getColumnDimension('A')->setWidth(15);
                $active_sheet->getColumnDimension('B')->setWidth(15);
                $active_sheet->getColumnDimension('C')->setWidth(45);
                $active_sheet->getColumnDimension('D')->setWidth(20);
                $active_sheet->getColumnDimension('E')->setWidth(20);
                $active_sheet->getColumnDimension('F')->setWidth(10);
                $active_sheet->getColumnDimension('G')->setWidth(10);
                $active_sheet->getColumnDimension('H')->setWidth(30);
                $active_sheet->getColumnDimension('I')->setWidth(10);

                $active_sheet->setCellValue('A1', 'Дата');
                $active_sheet->setCellValue('B1', 'Договор');
                $active_sheet->setCellValue('C1', 'ФИО');
                $active_sheet->setCellValue('D1', 'Телефон');
                $active_sheet->setCellValue('E1', 'Почта');
                $active_sheet->setCellValue('F1', 'Сумма');
                $active_sheet->setCellValue('G1', 'ПК/НК');
                $active_sheet->setCellValue('H1', 'Менеджер');
                $active_sheet->setCellValue('I1', 'Статус');

                $i = 2;
                foreach ($contracts as $contract) {
                    if ($contract->client_status == 'pk') {
                        $client_status = 'ПК';
                    } elseif ($contract->client_status == 'nk') {
                        $client_status = 'НК';
                    } elseif ($contract->client_status == 'crm') {
                        $client_status = 'ПК CRM';
                    } elseif ($contract->client_status == 'rep') {
                        $client_status = 'Повтор';
                    } else {
                        $client_status = '';
                    }

                    if (!empty($contract->collection_status)) {
                        if (empty($contract->sold)) {
                            $status = 'МКК ';
                        } else {
                            $status = 'ЮК ';
                        }
                        $status .= $collection_statuses[$contract->collection_status];
                    } else {
                        $status = $statuses[$contract->status];
                    }

                    $active_sheet->setCellValue('A'.$i, date('d.m.Y', strtotime($contract->date)));
                    $active_sheet->setCellValue('B'.$i, $contract->number);
                    $active_sheet->setCellValue('C'.$i, $contract->lastname.' '.$contract->firstname.' '.$contract->patronymic.' '.$contract->birth);
                    $active_sheet->setCellValue('D'.$i, $contract->phone_mobile);
                    $active_sheet->setCellValue('E'.$i, $contract->email);
                    $active_sheet->setCellValue('F'.$i, $contract->amount*1);
                    $active_sheet->setCellValue('G'.$i, $client_status);
                    $active_sheet->setCellValue('H'.$i, $managers[$contract->manager_id]->name);
                    $active_sheet->setCellValue('I'.$i, $status);

                    $i++;
                }

                $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');

                $objWriter->save($this->config->root_dir.$filename);

                header('Location:'.$this->config->root_url.'/'.$filename);
                exit;
            }

            $this->design->assign('contracts', $contracts);
        }

        return $this->design->fetch('statistics/contracts.tpl');
    }

    private function action_payments()
    {
        if ($operation_id = $this->request->get('operation_id', 'integer')) {
            if ($operation = $this->operations->get_operation($operation_id)) {
                $operation->contract = $this->contracts->get_contract($operation->contract_id);
                $operation->transaction = $this->transactions->get_transaction($operation->transaction_id);
                if ($operation->transaction->insurance_id) {
                    $operation->transaction->insurance = $this->insurances->get_insurance($operation->transaction->insurance_id);
                }

                if ($operation->type == 'REJECT_REASON') {
                    $result = $this->soap1c->send_reject_reason($operation);
                    if (!((isset($result->return) && $result->return == 'OK') || $result == 'OK')) {
                        $order = $this->orders->get_order($operation->order_id);
                        $this->soap1c->send_order($order);
                        $result = $this->soap1c->send_reject_reason($operation);
                    }
                } else {
                    $result = $this->soap1c->send_payments(array($operation));
                }

                if ((isset($result->return) && $result->return == 'OK') || $result == 'OK') {
                    $this->operations->update_operation($operation->id, array(
                        'sent_date' => date('Y-m-d H:i:s'),
                        'sent_status' => 2
                    ));
                    $this->json_output(array('success' => 'Операция отправлена'));
                } else {
                    $this->json_output(array('error' => 'Ошибка при отправке'));
                }
            } else {
                $this->json_output(array('error' => 'Операция не найдена'));
            }
        } elseif ($daterange = $this->request->get('daterange')) {
            $search_filter = '';

            list($from, $to) = explode('-', $daterange);

            $date_from = date('Y-m-d', strtotime($from));
            $date_to = date('Y-m-d', strtotime($to));

            $this->design->assign('date_from', $date_from);
            $this->design->assign('date_to', $date_to);
            $this->design->assign('from', $from);
            $this->design->assign('to', $to);

            if ($search = $this->request->get('search')) {
                if (!empty($search['created'])) {
                    $search_filter .= $this->db->placehold(' AND DATE(t.created) = ?', date('Y-m-d', strtotime($search['created'])));
                }
                if (!empty($search['number'])) {
                    $search_filter .= $this->db->placehold(' AND c.number LIKE "%'.$this->db->escape($search['number']).'%"');
                }
                if (!empty($search['fio'])) {
                    $search_filter .= $this->db->placehold(' AND (u.lastname LIKE "%'.$this->db->escape($search['fio']).'%" OR u.firstname LIKE "%'.$this->db->escape($search['fio']).'%" OR u.patronymic LIKE "%'.$this->db->escape($search['fio']).'%")');
                }
                if (!empty($search['amount'])) {
                    $search_filter .= $this->db->placehold(' AND t.amount = ?', $search['amount'] * 100);
                }
                if (!empty($search['card'])) {
                    $search_filter .= $this->db->placehold(' AND t.callback_response LIKE "%'.$this->db->escape($search['card']).'%"');
                }
                if (!empty($search['register_id'])) {
                    $search_filter .= $this->db->placehold(' AND t.register_id LIKE "%'.$this->db->escape($search['register_id']).'%"');
                }
                if (!empty($search['operation'])) {
                    $search_filter .= $this->db->placehold(' AND t.operation LIKE "%'.$this->db->escape($search['operation']).'%"');
                }
                if (!empty($search['description'])) {
                    $search_filter .= $this->db->placehold(' AND t.description LIKE "%'.$this->db->escape($search['description']).'%"');
                }
            }

            $query = $this->db->placehold("
                SELECT
                    o.id,
                    o.user_id,
                    o.contract_id,
                    o.order_id,
                    o.transaction_id,
                    o.type,
                    o.amount,
                    t.created,
                    o.sent_date,
                    c.number AS contract_number,
                    u.lastname,
                    u.firstname,
                    u.patronymic,
                    u.birth,
                    t.register_id,
                    t.operation,
                    t.prolongation,
                    t.insurance_id,
                    t.description,
                    t.callback_response,
                    i.number AS insurance_number,
                    i.amount AS insurance_amount,
                    t.sector
                FROM __operations AS o
                LEFT JOIN __contracts AS c
                ON c.id = o.contract_id
                LEFT JOIN __users AS u
                ON u.id = o.user_id
                LEFT JOIN __transactions AS t
                ON t.id = o.transaction_id
                LEFT JOIN __insurances AS i
                ON i.id = t.insurance_id
                WHERE o.type != 'INSURANCE'
                $search_filter
                AND DATE(t.created) >= ?
                AND DATE(t.created) <= ?
                AND t.reason_code = 1
                ORDER BY t.created
            ", $date_from, $date_to);
            $this->db->query($query);

            $operations = array();
            foreach ($this->db->results() as $op) {
                if ($xml = simplexml_load_string($op->callback_response)) {
                    $op->pan = (string)$xml->pan;
                    $operations[$op->id] = $op;
                }
            }


            $statuses = $this->contracts->get_statuses();
            $this->design->assign('statuses', $statuses);

            $collection_statuses = $this->contracts->get_collection_statuses();
            $this->design->assign('collection_statuses', $collection_statuses);



            if ($this->request->get('download') == 'excel') {
                $managers = array();
                foreach ($this->managers->get_managers() as $m) {
                    $managers[$m->id] = $m;
                }

                $filename = 'files/reports/payments.xls';
                require $this->config->root_dir.'PHPExcel/Classes/PHPExcel.php';

                $excel = new PHPExcel();

                $excel->setActiveSheetIndex(0);
                $active_sheet = $excel->getActiveSheet();

                $active_sheet->setTitle("Выдачи ".$from."-".$to);

                $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);
                $excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                $active_sheet->getColumnDimension('A')->setWidth(15);
                $active_sheet->getColumnDimension('B')->setWidth(15);
                $active_sheet->getColumnDimension('C')->setWidth(45);
                $active_sheet->getColumnDimension('D')->setWidth(20);
                $active_sheet->getColumnDimension('E')->setWidth(20);
                $active_sheet->getColumnDimension('F')->setWidth(10);
                $active_sheet->getColumnDimension('G')->setWidth(10);
                $active_sheet->getColumnDimension('H')->setWidth(30);
                $active_sheet->getColumnDimension('I')->setWidth(10);

                $active_sheet->setCellValue('A1', 'Дата');
                $active_sheet->setCellValue('B1', 'Договор');
                $active_sheet->setCellValue('C1', 'ФИО');
                $active_sheet->setCellValue('D1', 'Сумма');
                $active_sheet->setCellValue('E1', 'Карта');
                $active_sheet->setCellValue('F1', 'Описание');
                $active_sheet->setCellValue('G1', 'B2P OrderID');
                $active_sheet->setCellValue('H1', 'B2P OperationID');
                $active_sheet->setCellValue('I1', 'Страховка');

                $i = 2;
                foreach ($operations as $contract) {
                    $active_sheet->setCellValue('A'.$i, date('d.m.Y', strtotime($contract->created)));
                    $active_sheet->setCellValue('B'.$i, $contract->contract_number.' '.($contract->sector == '7036' ? 'ЮК' : 'МКК'));
                    $active_sheet->setCellValue('C'.$i, $contract->lastname.' '.$contract->firstname.' '.$contract->patronymic.' '.$contract->birth);
                    $active_sheet->setCellValue('D'.$i, $contract->amount);
                    $active_sheet->setCellValue('E'.$i, $contract->pan);
                    $active_sheet->setCellValue('F'.$i, $contract->description.' '.($contract->prolongation ? '(пролонгация)' : ''));
                    $active_sheet->setCellValue('G'.$i, $contract->register_id);
                    $active_sheet->setCellValue('H'.$i, $contract->operation);//--
                    $active_sheet->setCellValue('I'.$i, $contract->insurance_number.' '.($contract->insurance_amount ? $contract->insurance_amount.' руб' : ''));

                    $i++;
                }

                $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');

                $objWriter->save($this->config->root_dir.$filename);

                header('Location:'.$this->config->root_url.'/'.$filename);
                exit;
            }



            $this->design->assign('operations', $operations);
        }

        return $this->design->fetch('statistics/payments.tpl');
    }

    private function action_eventlogs()
    {
        if ($daterange = $this->request->get('daterange')) {
            list($from, $to) = explode('-', $daterange);

            $date_from = date('Y-m-d', strtotime($from));
            $date_to = date('Y-m-d', strtotime($to));

            $this->design->assign('date_from', $date_from);
            $this->design->assign('date_to', $date_to);
            $this->design->assign('from', $from);
            $this->design->assign('to', $to);


            $query_manager_id = '';
            if ($filter_manager_id = $this->request->get('manager_id')) {
                if ($filter_manager_id != 'all') {
                    $query_manager_id = $this->db->placehold("AND o.manager_id = ?", (int)$filter_manager_id);
                }

                $this->design->assign('filter_manager_id', $filter_manager_id);
            }

            $query = $this->db->placehold("
                SELECT
                    o.id AS order_id,
                    o.date,
                    o.reason_id,
                    o.reject_reason,
                    o.user_id,
                    o.manager_id,
                    o.status,
                    u.lastname,
                    u.firstname,
                    u.patronymic
                FROM __orders AS o
                LEFT JOIN __users AS u
                ON u.id = o.user_id
                WHERE o.manager_id IS NOT NULL
                AND DATE(o.date) >= ?
                AND DATE(o.date) <= ?
                $query_manager_id
            ", $date_from, $date_to);
            $this->db->query($query);

            $orders = array();
            foreach ($this->db->results() as $o) {
                $orders[$o->order_id] = $o;
            }

            if (!empty($orders)) {
                foreach ($orders as $o) {
                    $o->eventlogs = $this->eventlogs->get_logs(array('order_id'=>$o->order_id));
                }
            }

            $events = $this->eventlogs->get_events();
            $this->design->assign('events', $events);

            $reasons = $this->reasons->get_reasons();
            $this->design->assign('reasons', $reasons);


            if ($this->request->get('download') == 'excel') {
                $managers = array();
                foreach ($this->managers->get_managers() as $m) {
                    $managers[$m->id] = $m;
                }

                $order_statuses = $this->orders->get_statuses();

                $filename = 'files/reports/eventlogs.xls';
                require $this->config->root_dir.'PHPExcel/Classes/PHPExcel.php';

                $excel = new PHPExcel();

                $excel->setActiveSheetIndex(0);
                $active_sheet = $excel->getActiveSheet();

                $active_sheet->setTitle("Логи ".$from."-".$to);

                $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);
                $excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                $active_sheet->getColumnDimension('A')->setWidth(6);
                $active_sheet->getColumnDimension('B')->setWidth(30);
                $active_sheet->getColumnDimension('C')->setWidth(10);
                $active_sheet->getColumnDimension('D')->setWidth(10);
                $active_sheet->getColumnDimension('E')->setWidth(30);
                $active_sheet->getColumnDimension('F')->setWidth(30);

                $active_sheet->setCellValue('A1', '#');
                $active_sheet->setCellValue('B1', 'Заявка');
                $active_sheet->mergeCells('C1:F1');
                $active_sheet->setCellValue('C1', 'События');

                $style_bold = array(
                    'font' => array(
                        'name' => 'Calibri',
                        'size'=>13,
                        'bold'=>true
                    ),
                    'alignment' => array (
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'wrap'          => true,
                    )
                );
                $active_sheet->getStyle('A1:C1')->applyFromArray($style_bold);

                $i = 2;
                $rc = 1;
                foreach ($orders as $order) {
                    $start_i = $i;

                    $a_indexes = 'A'.$i.':A'.($i+count($order->eventlogs) - 1);
                    if (count($order->eventlogs) > 2) {
                        $active_sheet->mergeCells($a_indexes);
                    }
                    $active_sheet->getStyle($a_indexes)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $active_sheet->getStyle($a_indexes)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                    $active_sheet->setCellValue('A'.$i, $rc);

                    $b_indexes = 'B'.($i+3).':B'.($i+count($order->eventlogs)-1);
                    if (count($order->eventlogs) > 2) {
                        $active_sheet->mergeCells($b_indexes);
                    }
                    $active_sheet->getStyle($b_indexes)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $active_sheet->getStyle($b_indexes)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                    $active_sheet->setCellValue('B'.$i, $order->order_id);
                    $active_sheet->setCellValue('B'.($i+1), 'Статус: '.$order_statuses[$order->status]);
                    $active_sheet->setCellValue('B'.($i+2), 'Менеджер: '.$managers[$order->manager_id]->name);

                    foreach ($order->eventlogs as $ev) {
                        $active_sheet->setCellValue('C'.$i, date('d.m.Y', strtotime($ev->created)));
                        $active_sheet->setCellValue('D'.$i, date('H:i:s', strtotime($ev->created)));
                        $active_sheet->setCellValue('E'.$i, $events[$ev->event_id]);
                        $active_sheet->setCellValue('F'.$i, $managers[$ev->manager_id]->name);

//                        $active_sheet->getStyle('C'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//                        $active_sheet->getStyle('D'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//                        $active_sheet->getStyle('E'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//                        $active_sheet->getStyle('F'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//
                        $i++;
                    }

                    $rc++;

                    $active_sheet->getStyle('A'.$start_i.':F'.($i-1))->applyFromArray(
                        array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                                    'color' => array('rgb' => '666666')
                                )
                            )
                        )
                    );
                    $active_sheet->getStyle('A'.$start_i.':F'.($i-1))->applyFromArray(
                        array(
                            'borders' => array(
                                'top' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_DOUBLE ,
                                    'color' => array('rgb' => '222222')
                                ),
                                'bottom' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_DOUBLE ,
                                    'color' => array('rgb' => '222222')
                                ),
                                'left' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_DOUBLE ,
                                    'color' => array('rgb' => '222222')
                                ),
                                'right' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_DOUBLE ,
                                    'color' => array('rgb' => '222222')
                                )
                            )
                        )
                    );
//                    $active_sheet->getStyle()->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                }

                $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');

                $objWriter->save($this->config->root_dir.$filename);

                header('Location:'.$this->config->root_url.'/'.$filename);
                exit;
            }



            $this->design->assign('orders', $orders);
        }

        return $this->design->fetch('statistics/eventlogs.tpl');
    }

    private function action_penalties()
    {
        if ($daterange = $this->request->get('daterange')) {
            list($from, $to) = explode('-', $daterange);

            $date_from = date('Y-m-d', strtotime($from));
            $date_to = date('Y-m-d', strtotime($to));

            $this->design->assign('date_from', $date_from);
            $this->design->assign('date_to', $date_to);
            $this->design->assign('from', $from);
            $this->design->assign('to', $to);


            $filter = array();
            $filter['date_from'] = $date_from;
            $filter['date_to'] = $date_to;
            $filter['status'] = array(2,3,4);

            if ($this->manager->role == 'user' || $this->manager->role == 'big_user') {
                $filter['manager_id'] = $this->manager->id;
            } elseif ($filter_manager_id = $this->request->get('manager_id')) {
                if ($filter_manager_id != 'all') {
                    $filter['manager_id'] = $filter_manager_id;
                }

                $this->design->assign('filter_manager_id', $filter_manager_id);
            }

            $orders = array();
            if ($penalties = $this->penalties->get_penalties($filter)) {
                $order_ids = array();
                foreach ($penalties as $penalty) {
                    $order_ids[] = $penalty->order_id;
                }

                foreach ($this->orders->get_orders(array('id' => $order_ids)) as $order) {
                    $order->penalties = array();
                    $orders[$order->order_id] = $order;
                }

                foreach ($penalties as $penalty) {
                    if (isset($orders[$penalty->order_id])) {
                        $orders[$penalty->order_id]->penalties[] = $penalty;
                    }
                }

                $total_summ = 0;
                $total_count = 0;
                foreach ($orders as $order) {
                    $total_count++;
                    $order->penalty_summ = 0;
                    foreach ($order->penalties as $p) {
                        if ($order->status == 7) {
                            $p->cost = 0;
                        }

                        if ($p->status == 2 || $p->status == 3) {
                            $p->cost = 0;
                        }

                        if ($order->penalty_summ < $p->cost) {
                            $order->penalty_summ = $p->cost;
                        }
                    }
                    $order->penalty_summ = min($order->penalty_summ, 500);
                    $total_summ += $order->penalty_summ;
                }

                $this->design->assign('total_summ', $total_summ);
                $this->design->assign('total_count', $total_count);
            }

            $this->design->assign('orders', $orders);

            $penalty_types = array();
            foreach ($this->penalties->get_types() as $t) {
                $penalty_types[$t->id] = $t;
            }
            $this->design->assign('penalty_types', $penalty_types);

            $penalty_statuses = $this->penalties->get_statuses();
            $this->design->assign('penalty_statuses', $penalty_statuses);

            $managers = array();
            foreach ($this->managers->get_managers() as $m) {
                $managers[$m->id] = $m;
            }
            uasort($managers, function ($a, $b) {
                return strcasecmp($a->name_1c, $b->name_1c);
            });
            $this->design->assign('managers', $managers);
        }

        if ($this->request->get('download') == 'excel') {
            $managers = array();
            foreach ($this->managers->get_managers() as $m) {
                $managers[$m->id] = $m;
            }

            $filename = 'files/reports/penalties.xls';
            require $this->config->root_dir . 'PHPExcel/Classes/PHPExcel.php';

            $excel = new PHPExcel();

            $excel->setActiveSheetIndex(0);
            $active_sheet = $excel->getActiveSheet();

            $active_sheet->setTitle("Штрафы " . $from . "-" . $to);

            $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);
            $excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $active_sheet->getColumnDimension('A')->setWidth(30);
            $active_sheet->getColumnDimension('B')->setWidth(30);
            $active_sheet->getColumnDimension('C')->setWidth(10);
            $active_sheet->getColumnDimension('D')->setWidth(10);
            $active_sheet->getColumnDimension('E')->setWidth(20);
            $active_sheet->getColumnDimension('F')->setWidth(10);
            $active_sheet->getColumnDimension('G')->setWidth(30);
            $active_sheet->getColumnDimension('H')->setWidth(30);
            $active_sheet->getColumnDimension('I')->setWidth(10);
            $active_sheet->getColumnDimension('J')->setWidth(10);
            $active_sheet->getColumnDimension('K')->setWidth(30);


            $active_sheet->mergeCells('A1:E1');
            $active_sheet->mergeCells('F1:K1');
            $active_sheet->setCellValue('A1', 'Заявка');
            $active_sheet->setCellValue('F1', 'Штрафы');

            $orders_i = 2;
            $penalties_i = 2;

            $order_status = ['Новая', 'Принята', 'Одобрена', 'Отказ', 'Подписан', 'Выдан', 'Не удалось выдать', 'Погашен', 'Отказ клиента'];

            foreach ($orders as $order) {
                $order_fio = "$order->lastname, $order->firstname, $order->patronymic";

                $active_sheet->setCellValue('A'.$orders_i, date('d.m.Y h:i:s', strtotime($order->date)));
                $active_sheet->setCellValue('B'.$orders_i, $order_fio);
                $active_sheet->setCellValue('C'.$orders_i, $order->order_id);
                $active_sheet->setCellValue('D'.$orders_i, $order_status[$order->status]);
                $active_sheet->setCellValue('E'.$orders_i, "$order->penalty_summ Р");

                foreach ($order->penalties as $penalty) {
                    $active_sheet->setCellValue('F'.$penalties_i, $penalty->created);
                    $active_sheet->setCellValue('G'.$penalties_i, $managers[$penalty->manager_id]->name);
                    $active_sheet->setCellValue('H'.$penalties_i, $penalty->comment);
                    $active_sheet->setCellValue('I'.$penalties_i, $this->penalties->get_statuses($penalty->status));
                    $active_sheet->setCellValue('J'.$penalties_i, "$penalty->cost Р");
                    $active_sheet->setCellValue('K'.$penalties_i, $managers[$penalty->control->manager_id]->name);

                    $penalties_i++;
                }
                $orders_i++;
            }

            $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');

            $objWriter->save($this->config->root_dir.$filename);

            header('Location:'.$this->config->root_url.'/'.$filename);
            exit;
        }

        return $this->design->fetch('statistics/penalties.tpl');
    }
}
