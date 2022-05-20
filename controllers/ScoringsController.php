<?php

class ScoringsController extends Controller
{
    public function fetch()
    {

        if ($this->request->method('post')) {
            $scoring_settings = $this->request->post('settings');
            
            
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($scoring_settings);echo '</pre><hr />';
            
            foreach ($scoring_settings as $scoring_type) {
                $update_item = new StdClass();
                $update_item->active = isset($scoring_type['active']) ? (int)$scoring_type['active'] : 0;
                $update_item->params = isset($scoring_type['params']) ? (array)$scoring_type['params'] : array();
                $update_item->negative_action = (string)$scoring_type['negative_action'];
                
                $this->scorings->update_type($scoring_type['id'], $update_item);
            }
            

            if ($positions = $this->request->post('position')) {
                $i = 1;
                foreach ($positions as $pos => $id) {
                    $this->scorings->update_type($id, array('position' => $i++));
                }
            }
        } else {
            $scoring_settings = $this->settings->scoring_settings;
        }
        
        if (!empty($scoring_settings)) {
            $this->design->assign('scoring_settings', $scoring_settings);
        }
        
        $scoring_types = $this->scorings->get_types();
        $this->design->assign('scoring_types', $scoring_types);
        
  
        return $this->design->fetch('scorings.tpl');
    }
}
