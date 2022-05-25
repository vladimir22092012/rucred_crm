<?php

class AjaxScoringsController extends Controller
{
    private $response = array();
    
    public function fetch()
    {
    	$action = $this->request->get('action', 'string');
        
        switch ($action):
            
            case 'create':
            
                $type = $this->request->get('type', 'string');
                $order_id = $this->request->get('order_id', 'integer');
                
                $scoring_types = $this->scorings->get_types();
                
                if ($order = $this->orders->get_order($order_id))
                {
                    switch ($type):
                        
                        case 'free':
                            
                            foreach ($scoring_types as $scoring_type)
                            {
                                if ($scoring_type->type == 'first')
                                {
                                    $add_scoring = array(
                                        'user_id' => $order->user_id,
                                        'order_id' => $order->order_id,
                                        'type' => $scoring_type->name,
                                        'status' => 'new',
                                        'start_date' => date('Y-m-d H:i:s'),
                                    );
                                    $this->scorings->add_scoring($add_scoring);
                                }
                            }
                            $this->response['success'] = 1;
                            
                        break;
                        
                        case 'all':
                        
                            foreach ($scoring_types as $scoring_type)
                            {
                                $add_scoring = array(
                                    'user_id' => $order->user_id,
                                    'order_id' => $order->order_id,
                                    'type' => $scoring_type->name,
                                    'status' => 'new',
                                    'start_date' => date('Y-m-d H:i:s'),
                                );
                                $this->scorings->add_scoring($add_scoring);
                            }
                            $this->response['success'] = 1;
                            
                        break;
                        
                        case 'local_time':
                        case 'location':
                        case 'fms':
                        case 'fns':
                        case 'fssp':
                        case 'scorista':
                        case 'juicescore':
                        case 'whitelist':
                        case 'blacklist':
                        case 'efrsb':
                        case 'antirazgon':
                        case 'nbki':
                        case 'employer':
                        case 'okb':
                        case 'rfmlist':
                            
                            $add_scoring = array(
                                'user_id' => $order->user_id,
                                'order_id' => $order->order_id,
                                'type' => $type,
                                'status' => 'new',
                                'start_date' => date('Y-m-d H:i:s'),
                            );
                            $this->scorings->add_scoring($add_scoring);

                            $this->response['success'] = 1;
                            
                            
                        break;
                        
                    endswitch;
                }
                else
                {
                    $this->response['error'] = 'undefined_order';
                }
                
            break;
            
            case 'get_body':
                
                $scoring_id = $this->request->get('scoring_id', 'integer');
                
                if ($scoring = $this->scorings->get_scoring($scoring_id))
                {
                    $scoring->body = unserialize($scoring->body);
                    
                    $this->response['success'] = 1;
                    $this->response['body'] = $scoring->body;
                }
                else
                {
                    $this->response['error'] = 1;
                    $this->response['message'] = 'Не найден скоринг';
                }
                
            break;
            
        endswitch;
    
        $this->json_output($this->response);
        
    }
    
}