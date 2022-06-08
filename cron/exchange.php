<?php
error_reporting(-1);
ini_set('display_errors', 'On');


chdir(dirname(__FILE__).'/../');

require __DIR__ . '/../vendor/autoload.php';

class ExchangeCron extends Core
{
    public function __construct()
    {
    	parent::__construct();
        
        $this->send_loans();
            
    }
    
    
    /**
     * ExchangeCron::send_loans()
     * Отправляем выданные займы
     * @return void
     */
    private function send_loans()
    {
        $order_limit = 5;
        
        $query = $this->db->placehold("
            SELECT id
            FROM __orders
            WHERE sent_1c = 0
            AND status != 12
            ORDER BY date DESC
            LIMIT ?
        ", $order_limit);
        $this->db->query($query);

        if ($orders = $this->db->results('id'))
        {
            foreach ($orders as $order_id)
            {
                $result = $this->soap1c->send_loan($order_id);
                if (isset($result->return) && $result->return == 'OK')
                {
                    $this->orders->update_order($order_id, array(
                        'sent_1c' => 2
                    ));
                }
            }
        }
    
    }
    
}
new ExchangeCron();