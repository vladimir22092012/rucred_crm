<?php

class Location_scoring extends Core
{
    private $user_id;
    private $order_id;
    private $audit_id;
    private $type;
    private $exception_regions;
    
    public function run_scoring($scoring_id)
    {
        $update = array();
        
    	$scoring_type = $this->scorings->get_type('location');
        
        if ($scoring = $this->scorings->get_scoring($scoring_id))
        {
            if ($order = $this->orders->get_order((int)$scoring->order_id))
            {
                $regaddress = $this->addresses->get_address($order->regaddress_id);                
                
                if (empty($regaddress->region))
                {
                    $update = array(
                        'status' => 'error',
                        'string_result' => 'в заявке не указан регион регистрации'
                    );
                }
                else
                {
                    $exception_regions = array_map('trim', explode(',', $scoring_type->params['regions']));
                
                    $score = !in_array(mb_strtolower(trim($regaddress->region), 'utf8'), $exception_regions);
                
                    $update = array(
                        'status' => 'completed',
                        'body' => serialize(array('region' => $regaddress->region)),
                        'success' => $score
                    );
                    if ($score)
                        $update['string_result'] = 'Допустимый регион: '.$regaddress->region;
                    else
                        $update['string_result'] = 'Недопустимый регион: '.$regaddress->region;

                }
                
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
        
    private function scoring($region_name)
    {
        $score = !in_array(mb_strtolower($region_name, 'utf8'), $this->exception_regions);
        
        $add_scoring = array(
            'user_id' => $this->user_id,
            'audit_id' => $this->audit_id,
            'type' => 'location',
            'body' => $region_name,
            'success' => (int)$score
        );
        if ($score)
        {
            $add_scoring['string_result'] = 'Допустимый регион: '.$region_name;
        }
        else
        {
            $add_scoring['string_result'] = 'Недопустимый регион: '.$region_name;
        }
        
        $this->scorings->add_scoring($add_scoring);
        
        return $score;
    }

}