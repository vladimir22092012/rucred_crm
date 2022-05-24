<?php

class Employer_scoring extends Core
{
    
    public function run_scoring($scoring_id)
    {
        $update = array();
        
    	$scoring_type = $this->scorings->get_type('employer');
        
        if ($scoring = $this->scorings->get_scoring($scoring_id))
        {
            if ($order = $this->orders->get_order((int)$scoring->order_id))
            {
                $response = 'за 02.22 58 735 руб';
                
                $update = array(
                    'status' => 'completed',
                    'body' => serialize(array('response' => $response)),
                    'success' => 1
                );
                $update['string_result'] = $response;
                
                $this->scorings->update_scoring($scoring_id, $update);

                
            }
            else
            {
                $update = array(
                    'status' => 'error',
                    'string_result' => 'не найдена заявка'
                );
            }
            
            if (!empty($update))
                $this->scorings->update_scoring($scoring_id, $update);
            
            return $update;

        }
    }
    
}