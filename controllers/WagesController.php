<?php

class WagesController extends Controller
{
    public function fetch()
    {
    	$years = array();
        $start = 2021;
        $current_year = date('Y');
        for ($i = $start; $i <= $current_year; $i++)
            $years[] = $i;
        $this->design->assign('years', $years);
        $this->design->assign('filter_year', $current_year);
        
        $monthes = array(
            1 => 'январь',
            2 => 'февраль',
            3 => 'март',
            4 => 'апрель',
            5 => 'май',
            6 => 'июнь',
            7 => 'июль',
            8 => 'август',
            9 => 'сентябрь',
            10 => 'октябрь',
            11 => 'ноябрь',
            12 => 'декабрь',
        );
        $this->design->assign('monthes', $monthes);
        $current_month = date('m');
        $this->design->assign('filter_month', $current_month);
        
        
        if ($this->request->get('report'))
        {
            $filter_manager_id = $this->request->get('manager_id');
            $filter_year = $this->request->get('year');
            $filter_month = $this->request->get('month');

            $this->design->assign('filter_year', $filter_year);
            $this->design->assign('filter_month', $filter_month);
            $this->design->assign('filter_manager_id', $filter_manager_id);
            
            if (empty($filter_manager_id))
            {
                $this->design->assign('error', 'Выберите сотрудника для формирования отчета');
            }
            elseif (empty($filter_year))
            {
                $this->design->assign('error', 'Выберите год для формирования отчета');
            }
            elseif (empty($filter_month))
            {
                $this->design->assign('error', 'Выберите месяц для формирования отчета');
            }
            else
            {
                $report = new StdClass();
                $report->contracts = array();
                $report->total_inssuance = 0;
                $report->total_nk = 0;
                $report->total_pk = 0;
                $report->total_percents = 0;
                $report->total_wage = 0;
                
                $month_count = cal_days_in_month(CAL_GREGORIAN, $filter_month, $filter_year);
                $from_date = $filter_year.'-'.($filter_month < 10 ? '0'.$filter_month : $filter_month).'-01';
                $to_date = $filter_year.'-'.($filter_month < 10 ? '0'.$filter_month : $filter_month).'-'.$month_count;
                $days = array();
                for ($i = 1; $i <= $month_count; $i++)
                {
                    $index = $filter_year.($filter_month < 10 ? '0'.$filter_month : $filter_month).($i < 10 ? '0'.$i : $i);

                    $days[$index] = new StdClass();
                    $days[$index]->contracts = array();
                    $days[$index]->day_inssuance = 0;
                    $days[$index]->day_pk = 0;
                    $days[$index]->day_nk = 0;
                    
                    $days[$index]->day_percents = 0;
                    
                    $days[$index]->date = $filter_year.'-'.($filter_month < 10 ? '0'.$filter_month : $filter_month).'-'.($i < 10 ? '0'.$i : $i);
                }
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($days);echo '</pre><hr />';                
                $report->days = $days;
                
                
                // выдачи                
                $query = $this->db->placehold("
                    SELECT *
                    FROM __contracts AS c
                    LEFT JOIN __orders AS o
                    ON o.id = c.order_id
                    WHERE c.status IN (?@)
                    AND o.manager_id = ?
                    AND DATE(c.inssuance_date) >= ?
                    AND DATE(c.inssuance_date) <= ?
                ", array(2,3,4), $filter_manager_id, $from_date, $to_date);
                $this->db->query($query);
                
                if ($contracts = $this->db->results())
                {
                    foreach ($contracts as $contract)
                    {
                        $index = date('Ymd', strtotime($contract->inssuance_date));
                        
                        $report->days[$index]->day_inssuance += $contract->amount;
                        $report->total_inssuance += $contract->amount;
                        $report->days[$index]->contracts[] = $contract;
                        
                        if ($contract->client_status == 'nk')
                        {
                            $report->days[$index]->day_nk++;
                            $report->total_nk++; 
                        }
                        if ($contract->client_status == 'pk' || $contract->client_status == 'crm')
                        {
                            $report->days[$index]->day_pk++;
                            $report->total_pk++; 
                        }
                    }
                }
                
                
                // проценты
                $query = $this->db->placehold("
                    SELECT 
                        t.loan_body_summ,
                        t.loan_percents_summ,
                        t.loan_peni_summ,
                        t.loan_charge_summ,
                        o.created 
                    FROM __transactions AS t
                    LEFT JOIN __operations AS o
                    ON o.transaction_id = t.id
                    LEFT JOIN __orders AS ord
                    ON o.order_id = ord.id
                    WHERE ord.manager_id = ?
                    AND DATE(o.created) >= ?
                    AND DATE(o.created) <= ?
                ", $filter_manager_id, $from_date, $to_date);
                $this->db->query($query);
                
                if ($payments = $this->db->results())
                {
                    foreach ($payments as $payment)
                    {
                        $index = date('Ymd', strtotime($payment->created));
                        
                        $days[$index]->day_percents += $payment->loan_percents_summ + $payment->loan_charge_summ + $payment->loan_peni_summ;
                        $report->total_percents += $payment->loan_percents_summ + $payment->loan_charge_summ + $payment->loan_peni_summ;
                    }
                }
                
                                
                $report->coef = $report->total_percents / $report->total_inssuance;
                
                if ($report->coef < 0.36)
                    $stavka = 0;
                elseif ($report->coef < 0.38)
                    $stavka = 3;
                elseif ($report->coef < 0.4)
                    $stavka = 4;
                else
                    $stavka = 5;
                
                foreach ($report->days as $day)
                {
                    $day->percents_zp = round($day->day_percents * $stavka / 100, 2);
                    
                    $day->day_wage = $day->percents_zp;
                    $report->total_wage += $day->day_wage;
                }
                
                $report->card = 199;
                $report->sbor_dr = 100;
                
                $report->premia_nk = 0;
                if ($report->total_nk > 30) // план по новым клиентам 30
                    $report->premia_nk = ($report->total_nk - 30) * 250;
                
                $report->premia_sbor = 0;
                if ($report->coef > 0.36)
                {
                    if ($report->total_inssuance > 450000)
                        $report->premia_sbor = 4000;
                    elseif ($report->total_inssuance > 400000)
                        $report->premia_sbor = 3500;
                    elseif ($report->total_inssuance > 350000)
                        $report->premia_sbor = 3000;
                    elseif ($report->total_inssuance > 300000)
                        $report->premia_sbor = 2500;
/*                
                Если РЕЙТИНГ >= 36 тогда:
Если ОД (в пункту 5) >= 450000 то "Премия за сбор 36%" = 4000
Если ОД (в пункту 5) >= 400000 то "Премия за сбор 36%" = 3500
Если ОД (в пункту 5) >= 350000 то "Премия за сбор 36%" = 3000
Если ОД (в пункту 5) >= 300000 то "Премия за сбор 36%" = 2500
*/
                    
                }
                
                $report->total = $report->total_wage + $report->card + $report->premia_nk + $report->premia_sbor - $report->sbor_dr;

                
                
                $this->design->assign('report', $report);
            }
        }
        
        
        return $this->design->fetch('offline/wages.tpl');
    }
    
}