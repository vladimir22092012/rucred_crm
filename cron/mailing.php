<?php
error_reporting(-1);
ini_set('display_errors', 'On');

chdir(dirname(__FILE__).'/../');

require 'autoload.php';

class MailingCron extends Core
{
    public function __construct()
    {
    	parent::__construct();
        
        $this->run();
    }
    
    private function run()
    {
        $i = 1000;
        $mailing = true;
        while ($i > 0 && !empty($mailing))
        {
            if ($mailing = $this->mailings->get_status_item(1))
            {
                $user = $this->users->get_user($mailing->user_id);
                
                $client_time = $this->helpers->get_regional_time($user->Regregion);
                $client_time_warning = $this->users->get_time_warning($client_time);                    
                                
                if (empty($client_time_warning))
                {
                    if ($this->communications->check_user($user->id))
                    {
                        if ($mailing->type == 'sms')
                        {
                            $sent_result = $this->sms_send($mailing->phone, $mailing->text, $mailing->yuk);
                            $update_status = 2;
                            
                            $this->communications->add_communication(array(
                                'user_id' => $mailing->user_id,
                                'manager_id' => $mailing->manager_id,
                                'created' => date('Y-m-d H:i:s'),
                                'type' => 'sms',
                                'content' => $mailing->text,
                                'from_number' => $this->sms->get_originator($mailing->yuk),
                                'to_number' => $mailing->phone,
                                'yuk' => $mailing->yuk,
                                'result' => serialize($sent_result),
                            ));

                            $this->comments->add_comment(array(
                                'order_id' => 0,
                                'user_id' => $mailing->user_id,
                                'manager_id' => 100,
                                'text' => 'Смс: '.$mailing->text,
                                'created' => date('Y-m-d H:i:s'),
                                'organization' => empty($mailing->yuk) ? 'mkk' : 'yuk',
                                'auto' => 1,
                            ));
                        }
                        
                        if ($mailing->type == 'zvonobot')
                        {
                            $sent_result = $this->zvonobot_call($mailing->phone, $mailing->text, $mailing->yuk);                    
                            $update_status = 2;

                            $zvonobot_id = $this->zvonobot->add_zvonobot(array(
                                'user_id' => 0,
                                'contract_id' => 0,
                                'yuk' => $mailing->yuk,
                                'zvonobot_id' => isset($sent_result['data'][0]['id']) ? $sent_result['data'][0]['id'] : null,
                                'status' => isset($sent_result['data'][0]['status']) ? $sent_result['data'][0]['status'] : 'new',
                                'body' => serialize($sent_result),
                                'create_date' => date('Y-m-d H:i:s'),
                                'phone' => $mailing->phone,
                            ));
                            
                            $this->communications->add_communication(array(
                                'user_id' => $mailing->user_id,
                                'manager_id' => $mailing->manager_id,
                                'created' => date('y-m-d H:i:s'),
                                'type' => 'zvonobot',
                                'content' => $mailing->text,
                                'outer_id' => $zvonobot_id,
                                'from_number' => $this->zvonobot->get_outgoing_phone($mailing->yuk),
                                'to_number' => $mailing->phone,
                                'yuk' => $mailing->yuk,
                                'result' => serialize($sent_result),
                            ));
                            
                            $this->comments->add_comment(array(
                                'order_id' => 0,
                                'user_id' => $mailing->user_id,
                                'manager_id' => 0,
                                'text' => 'Звонобот: '.$mailing->text,
                                'created' => date('Y-m-d H:i:s'),
                                'organization' => empty($mailing->yuk) ? 'mkk' : 'yuk',
                            ));
                            
                            usleep(200);
                        }
                    }
                    else
                    {
                        $update_status = 5; // исчерпан лимит коммуникаций
                    }
                }
                else
                {
                    $update_status = 4; // неподходящее время клиента
                }
                
                

                    
                $this->mailings->update_item($mailing->id, array(
                    'sent_result' => empty($sent_result) ? '' : serialize($sent_result),
                    'sent' => date('Y-m-d H:i:s'),
                    'status' => $update_status,
                ));
            }
            
            $i--;
        }
    }
    
    private function sms_send($number, $text, $yuk = 0)
    {
//        return null;
        
        return $this->sms->send($number, $text, $yuk);
    }
    
    private function zvonobot_call($number, $text, $yuk = 0)
    {
//        return null;
        
        $name = 'mailing_'.date('ymd');
    	$record_id = $this->zvonobot->create_record($name, $text, $yuk);

        return $this->zvonobot->call($number, $record_id['data']['id'], $yuk);
    }

}
new MailingCron();