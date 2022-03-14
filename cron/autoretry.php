<?php
error_reporting(-1);
ini_set('display_errors', 'On');

/*
1. за последние 14 дней был погашенный займ
2. запуск и прохождение повторной проверки: Чёрный список, ФМС и ФССП , 
3. изменял ли клиент анкетные данные и карту зачисления? 
(тут надо понимать как понимать историю данных из 1с, потому что в рамках срм-ных заявок - 
мы можем просто сравнить историю)
4. НЕ исполнилось 20 и 45 лет на момент подачи заявки.
5. Предыдущий займ был НЕ 10 000 рублей
6. С момента последнего рассмотрения (дата одобрения) прошло до 6 месяцев

Определение суммы одобрения:
- минимальная сумма получения 3000 (нельзя меньше)
- если срок пользования предыдущего займа ОТ 14 дней, то максимум можем добавить 4 000 руб. к новому займу, 
- если срок пользования предыдущего займа ДО 14 дней, то можем добавить максимум 2 000 руб. к новому займу.
Максимальный лимит увеличения суммы займа за месяц: 10 000 руб. 
и максимальная сумма одобренного займа за месяц до 10 000 руб., 
отсчет идет от 1-ой выдачи количество займов не учитывается.

*/

chdir(dirname(__FILE__).'/../');

require 'autoload.php';

class AutoretryCron extends Core
{
    public function __construct()
    {
    	parent::__construct();
        
        if ($this->request->get('test'))
            $this->test();
        else
            $this->run();
    }
    
    private function run()
    {
    	if ($orders = $this->orders->get_orders(array('autoretry' => 1)))
        {
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($orders);echo '</pre><hr />';
            foreach ($orders as $order)
            {
                // проверяем завершены ли уже скоринги, если нет переходим к следующей
                $scorings = $this->scorings->get_scorings(array('order_id' => $order->order_id, 'type' => array('fssp', 'fms')));
                $completed_scorings = 1;
                foreach ($scorings as $scoring)
                    if (in_array($scoring->status, array('new', 'process')))
                        $completed_scorings = 0;
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($scorings);echo '</pre><hr />';
                if ($completed_scorings)
                {
                    if ($this->check_close_contract($order))
                    {
                        if ($this->check_age($order))
                        {
                            if ($this->check_anketa($order))
                            {
                                if ($this->check_last_date($order))
                                {
                                    if ($this->check_last_summ($order))
                                    {
                                        if ($max_amount = $this->calc_max_amount($order))
                                        {
                                            $this->orders->update_order($order->order_id, array(
                                                'autoretry' => 0,
                                                'autoretry_result' => 'Рекомендуемая сумма к выдаче '.$max_amount.' руб',
                                                'autoretry_summ' => $max_amount,
                                            ));
                                            
                                            //TODO переводим заявку в одобренные

                                        }
                                        else
                                        {
                                            $this->orders->update_order($order->order_id, array(
                                                'autoretry' => 0,
                                                'autoretry_result' => 'За последний месяц лимит выдачи исчерпан',
                                            ));                                                               
                                        }
                                    }
                                    else
                                    {
                                        $this->orders->update_order($order->order_id, array(
                                            'autoretry' => 0,
                                            'autoretry_result' => 'Предыдущий займ был более 10 000 рублей',
                                        ));                                                                                                                                        
                                    }
                                }
                                else
                                {
                                    $this->orders->update_order($order->order_id, array(
                                        'autoretry' => 0,
                                        'autoretry_result' => 'С момента последнего рассмотрения прошло более 6 месяцев',
                                    ));                                                                                                
                                }
                            }
                            else
                            {
                                $this->orders->update_order($order->order_id, array(
                                    'autoretry' => 0,
                                    'autoretry_result' => 'Изменены данные или карта',
                                ));                                                            
                            }
                        }
                        else
                        {
                            $this->orders->update_order($order->order_id, array(
                                'autoretry' => 0,
                                'autoretry_result' => 'Клиенту исполнилось 20 или 45 лет на момент подачи заявки',
                            ));                            
                        }
                    }
                    else
                    {
                        $this->orders->update_order($order->order_id, array(
                            'autoretry' => 0,
                            'autoretry_result' => 'C даты закрытия последнего займа прошло более 14 дней',
                        ));
                    }
                    
                }
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($order);echo '</pre><hr />';                
            }
        }
    }
    
    /**
     * AutoretryCron::check_close_contract()
     * Проверяет прошло ли 14 дней с момента закрытия
     * 1. за последние 14 дней был погашенный займ
     * @param mixed $order
     * @return bolean
     */
    private function check_close_contract($order)
    {
        $last_item = null;
        
        $history_items = $this->contracts->get_contracts(array('user_id'=>$order->user_id, 'status'=>array(3,4,7)));
        foreach ($history_items as $history_item)
        {
            if (empty($last_item) || strtotime($last_item->close_date) > strtotime($history_item->close_date))
            {
                $last_item = $history_item;
            }
        }
        
        if (!empty($last_item))
        {
            $last_item_close = strtotime(date('Y-m-d 00:00:00', strtotime($last_item->close_date)));
            $today = strtotime(date('Y-m-d 00:00:00'));
    
        	$diff = ($today - $last_item_close) / 86400;// 
    
            return $diff <= 14;
        }
        return false;
    }
    
    /**
     * AutoretryCron::check_anketa()
     * Проверяет изменял ли клиент анкетные данные и карту зачисления?
     * @param mixed $order
     * @return bolean
     */
    private function check_anketa($order)
    {
        // TODO: Проверить 
        
                
    	return true;
    }
    
    /**
     * AutoretryCron::check_age()
     * Проверяет НЕ исполнилось 20 и 45 лет на момент подачи заявки.
     * @param mixed $order
     * @return bolean
     */
    private function check_age($order)
    {
        $last_item = null;
        $history_items = $this->contracts->get_contracts(array('user_id'=>$order->user_id, 'status'=>array(3,4,7)));
        foreach ($history_items as $history_item)
        {
            if (empty($last_item) || strtotime($last_item->close_date) > strtotime($history_item->close_date))
            {
                $last_item = $history_item;
            }
        }

        // проверяем 45 лет
        $last_item_date = strtotime($last_item->inssuance_date);
        $today_date = strtotime(date('Y-m-d 00:00:00'));
        $date_45 = strtotime('+45 year', date('Y-m-d 00:00:00', strtotime($order->birth)));
        if ($date_45 < $today_date && $date_45 > $last_item_date)
            return false;

        $date_25 = strtotime('+25 year', date('Y-m-d 00:00:00', strtotime($order->birth)));
        if ($date_25 < $today_date && $date_25 > $last_item_date)
            return false;
        
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump(date('Y-m-d', $date_45), date('Y-m-d H:i:s', $date_25));echo '</pre><hr />';
    	return true;
    }
    
    /**
     * AutoretryCron::check_last_summ()
     * Проверяет Предыдущий займ был НЕ 10 000 рублей
     * @param mixed $order
     * @return bolean
     */
    private function check_last_summ($order)
    {
        $last_item = null;
        foreach ($order->loan_history as $history_item)
        {
            if (empty($last_item) || strtotime($last_item->close_date) > strtotime($history_item->close_date))
            {
                $last_item = $history_item;
            }
        }
        
    	return $last_item->amount < 10000;
    }
    
    /**
     * AutoretryCron::check_last_date()
     * Проверяет С момента последнего рассмотрения (дата одобрения) прошло до 6 месяцев
     * @param mixed $order
     * @return bolean
     */
    private function check_last_date($order)
    {
        $last_item = null;
        foreach ($order->loan_history as $history_item)
        {
            if (empty($last_item) || strtotime($last_item->close_date) > strtotime($history_item->close_date))
            {
                $last_item = $history_item;
            }
        }
        
        $last_item_date = strtotime($last_item->date);
        $border_date = strtotime(date('Y-m-d 00:00:00', strtotime('-6 month')));
    	return $last_item_date > $border_date;
    }
    
    /**
     * AutoretryCron::calc_max_amount()
        Определение суммы одобрения:
        - минимальная сумма получения 3000 (нельзя меньше)
        - если срок пользования предыдущего займа ОТ 14 дней, то максимум можем добавить 4 000 руб. к новому займу, 
        - если срок пользования предыдущего займа ДО 14 дней, то можем добавить максимум 2 000 руб. к новому займу.
        Максимальный лимит увеличения суммы займа за месяц: 10 000 руб. 
        и максимальная сумма одобренного займа за месяц до 10 000 руб., 
        отсчет идет от 1-ой выдачи количество займов не учитывается.
     * @param mixed $order
     * @return bolean
     */
    private function calc_max_amount_old($order)
    {
        // TODO: Доделать
        $max_loan_summ = 10000;        
        $last_item = null;
        $last_month_date = strtotime('-1 month');
        foreach ($order->loan_history as $history_item)
        {
            if (empty($last_item) || strtotime($last_item->close_date) > strtotime($history_item->close_date))
            {
                $last_item = $history_item;
            }
            
            if ($last_month_date < strtotime($last_item->date))
            {
                $max_loan_summ -= $last_item->amount;
            }
        }
        if (!empty($last_item->close_date))
            $last_period = intval(strtotime($last_item->close_date) - strtotime($last_item->date)) / 86400;
        
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($last_period);echo '</pre><hr />';        
        if ($max_loan_summ < 3000)
            return false;
        
        if ($order->amount < 3000)
        {
            $max_amount = 3000;
    	}
        else
        {
            if ($last_period > 14)
                $max_amount = $order->amount + 4000;
            elseif ($last_period <= 14)
                $max_amount = $order->amount + 2000;
            else
                $max_amount = $order->amount;
        }
        
        return min($max_amount, $max_loan_summ);
    }

    /**
     * AutoretryCron::calc_max_amount()
        Определение суммы одобрения:
        Зависит от срока пользования
        до 11 дней пользования - прошлая сумма, не менее 5k
        до 50 дней просрочки +2k от закрытой суммы займа, НО НЕ МЕНЕЕ 5k
        от 50 дней просрочки - 5k        
        Максимальный лимит увеличения суммы займа за месяц: 10 000 руб. 
        и максимальная сумма одобренного займа за месяц до 10 000 руб., 
        отсчет идет от 1-ой выдачи количество займов не учитывается.
     * @param mixed $order
     * @return bolean
     */
    private function calc_max_amount($order)
    {
        $history_items = $this->contracts->get_contracts(array('user_id'=>$order->user_id, 'status'=>array(3,4,7)));
        if (!empty($history_items))
        {            
            $max_loan_summ = 10000;        
            $last_item = null;
            $last_month_date = strtotime('-1 month');
            foreach ($history_items as $history_item)
            {
                if (empty($last_item) || strtotime($last_item->close_date) > strtotime($history_item->close_date))
                {
                    $last_item = $history_item;
                }
                
                if ($last_month_date < strtotime($last_item->inssuance_date))
                {
                    $max_loan_summ -= $last_item->amount;
                }
            }
            if (!empty($last_item))
            {
                $last_period = intval((strtotime($last_item->close_date) - strtotime($last_item->inssuance_date)) / 86400);
                $expiration_period = intval((strtotime($last_item->close_date) - strtotime($last_item->return_date)) / 86400);
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($expiration_period ,$last_period);echo '</pre><hr />';
            }
            
            if ($max_loan_summ < 5000)
                return false;
            
            if ($order->amount < 5000)
            {
                $max_amount = 5000;
        	}
            else
            {
                if ($last_period < 11)
                    $max_amount = max($last_item->amount, 5000);
                elseif ($expiration_period >= 50){
                    $max_amount = 5000;
                } else
                    $max_amount = min(5000, $last_item->amount + 2000);
            }
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($history_items);echo '</pre><hr />';            
        }
        
        
        return min($max_amount, $max_loan_summ);
    }
    
    public function test()
    {
        $order_id = '171525';
        $order = $this->orders->get_order($order_id);
        
        $check_close_contract = $this->check_close_contract($order);
        
        $max_amount = $this->calc_max_amount($order);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($check_close_contract, $max_amount);echo '</pre><hr />';    
    }
    
}
new AutoretryCron();