<?php
error_reporting(-1);
ini_set('display_errors', 'On');


//chdir('/home/v/vse4etkoy2/nalic_eva-p_ru/public_html/');
chdir(dirname(__FILE__).'/../');

require 'autoload.php';

class AuditCron extends Core
{
    public function __construct()
    {
    	parent::__construct();
        
        file_put_contents($this->config->root_dir.'cron/log.txt', date('d-m-Y H:i:s').' AUDIT RUN'.PHP_EOL, FILE_APPEND);
    }
    
    
    public function run()
    {
        $datetime = date('Y-m-d H:i:s', time() - 300);
        
        $overtime_scorings = $this->scorings->get_overtime_scorings($datetime);
        if (!empty($overtime_scorings))
        {
            foreach ($overtime_scorings as $overtime_scoring)
            {
                if (in_array($overtime_scoring->type, array('fms', 'fns', 'fssp')) && $overtime_scoring->repeat_count < 2)
                {
                    $this->scorings->update_scoring($overtime_scoring->id, array(
                        'status' => 'repeat',
                        'body' => 'Истекло время ожидания',
                        'string_result' => 'Повторный запрос',
                        'repeat_count' => $overtime_scoring->repeat_count + 1,
                    ));
                    
                }
                else
                {
                    $this->scorings->update_scoring($overtime_scoring->id, array(
                        'status' => 'error',
                        'string_result' => 'Истекло время ожидания'
                    ));
                }
            }
        }
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($overtime_scorings);echo '</pre><hr />';

        $i = 30;
        while ($i > 0)
        {
            if ($scoring = $this->scorings->get_repeat_scoring())
            {
                $this->scorings->update_scoring($scoring->id, array(
                    'status' => 'process',
                    'start_date' => date('Y-m-d H:i:s')
                ));
                
                $classname = $scoring->type."_scoring";
                
                $scoring_result = $this->{$classname}->run_scoring($scoring->id);
            }
            $i--;
        }

        $i = 30;
        while ($i > 0)
        {
            if ($scoring = $this->scorings->get_new_scoring())
            {
                $this->scorings->update_scoring($scoring->id, array(
                    'status' => 'process',
                    'start_date' => date('Y-m-d H:i:s')
                ));
                
                $classname = $scoring->type."_scoring";
                
                $scoring_result = $this->{$classname}->run_scoring($scoring->id);
            }
            $i--;
        }
        
    }
    
    
    
    /*
    public function run()
    {
    	$audits = $this->scorings->get_audits(array('status'=>'new', 'limit'=>5));

        foreach ($audits as $audit)
            $this->scorings->update_audit($audit->id, array('status'=>'process'));

        foreach ($audits as $audit)
        {
            $this->run_audit($audit);
        }
    }
    */
    
    public function run_audit($audit)
    {
    	foreach ($audit->types as $type)
        {
            $scoring_type = $this->scorings->get_type($type);
            
            $classname = $type."_scoring";
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($classname);echo '</pre><hr />';
            $scoring_type_result = $this->$classname->run($audit->id, $audit->user_id, $audit->order_id);
            
            if (!$scoring_type_result && $scoring_type->negative_action == 'stop')
            {
                $this->scorings->update_audit($audit->id, array('status'=>'stopped'));
                return false;
            }
            
        }
        
        $this->scorings->update_audit($audit->id, array('status'=>'completed'));
        return true;
    }
    
}

$cron = new AuditCron();
$cron->run();
