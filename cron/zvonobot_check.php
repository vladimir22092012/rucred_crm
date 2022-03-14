<?php
error_reporting(-1);
ini_set('display_errors', 'On');


chdir(dirname(__FILE__).'/../');

require 'autoload.php';

class ZvonobotCheckCron extends Core
{
    private $record_id_zero;
    private $record_id_one;
    
    public function __construct()
    {
    	parent::__construct();
        
        $this->record_id_zero = '812862';
        $this->record_id_one = '812870';
        
        $this->run_check_status();
        
    }
    
    private function run_check_status()
    {
        if ($calls = $this->zvonobot->get_zvonobots(array('status' => 'new', 'sort' => 'date_desc')))
        {
            foreach ($calls as $call)
            {
                if (!empty($call->zvonobot_id) && !empty($call->contract_id))
                {
                    $contract = $this->contracts->get_contract((int)$call->contract_id);
                    
                    $resp = $this->zvonobot->check($call->zvonobot_id, $call->yuk);
                    
                    $status = $resp['data'][0]['calls'][0]['status'];
                    if ($status == 'finished')
                    {
                        $startedAt = $resp['data'][0]['calls'][0]['startedAt'];
                        $answeredAt = $resp['data'][0]['calls'][0]['answeredAt'];
                        $finishedAt = $resp['data'][0]['calls'][0]['finishedAt'];
                        
                        $talkTime = $finishedAt - $answeredAt;
                        
                        $this->zvonobot->update_zvonobot($call->id, array(
                            'status' => 'finished',
                            'result' => $talkTime
                        ));
                        
                        if ($talkTime > 0)
                        {
                            $this->communications->add_communication(array(
                                'user_id' => (int)$contract->user_id,
                                'type' => 'zvonobot',
                                'created' => $call->create_date,
                                'content' => $call->zvonobot_id
                            ));
                        }
                        
                        // отправляем смс
                        if ($talkTime > 0 && $talkTime <= 7)
                        {
                            if ($call->yuk)
                            {                            
                                if ($contract->collection_status == 1)
                                    $sms = "Уважаемый клиент, напоминаем, что сегодня дата оплаты по вашему договору займа. ООО «ЮК №1». 88002226091";
                                if ($contract->collection_status == 2)
                                    $sms = "Уважаемый клиент, уведомляем о наличии просроченной задолженности по вашему договору займа. Не нарушайте принятые вами условия договора. ООО «ЮК №1». 88002226091";
                            }
                            else
                            {
                                if ($contract->collection_status == 1)
                                    $sms = "Уважаемый клиент, напоминаем, что сегодня дата оплаты по вашему договору займа. ООО МКК «Наличное +». 88003332484";
                                if ($contract->collection_status == 2)
                                    $sms = "Уважаемый клиент, уведомляем о наличии просроченной задолженности по вашему договору займа. Не нарушайте принятые вами условия договора. ООО МКК «Наличное +». 88003332484";                            
                            }
                            $sms_result = $this->sms->send($call->phone, $sms, $call->yuk);
                            
                            $this->zvonobot->update_zvonobot($call->id, array(
                                'sms_text' => $sms,
                                'sms_sent' => date('Y-m-d H:i:s'),
                            ));

                            $this->communications->add_communication(array(
                                'user_id' => $contract->user_id,
                                'type' => 'sms',
                                'from_number' => $this->sms->get_originator($call->yuk),
                                'to_number' => $call->phone,
                                'created' => date('Y-m-d H:i:s'),
                                'content' => $sms,
                                'yuk' => $call->yuk,
                                'result' => serialize($sms_result),
                            ));
                        }
                    }
                    elseif ($status == 'canceled')
                    {
                        $this->zvonobot->update_zvonobot($call->id, array(
                            'status' => 'canceled',
                        ));
                        
                    }
                    
                    echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';
    //                exit;
                }
            }
        }
    }
}
new ZvonobotCheckCron();