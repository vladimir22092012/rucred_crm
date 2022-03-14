<?php

class Smssales extends Core
{
    public function get_queue_for_sending_sms($limit = 9){
        $query = $this->db->placehold("
            SELECT * FROM __sms_sales WHERE number_of < 2 AND created_at > '2022-01-18 00:00:00' AND updated_at < NOW() - INTERVAL 9 MINUTE LIMIT ?
        ", (int)$limit);
        $this->db->query($query);
        $result = $this->db->results();

        return $result;
    }

    public function send_smssales($order){
        $firstname = $order->firstname;
        $amount = $order->amount;
        
        $template = $this->sms->get_template(6);

        $message =  preg_replace('/{\\$firstname}/', $firstname, $template->template, -1, $count);//из шаблонов
        $message = preg_replace('/{\\$amount}/', $amount, $message, -1, $count);//из шаблонов

        $result = $this->sms->send($order->phone_mobile, $message);
        //var_dump($result);
        $this->save_smssales([
            'phone' => $order->phone_mobile,
            'message' => $message,
            'number_of' => 1,
            'firstname' => $order->firstname
        ]);
    }

    public function save_smssales($item)
    {
        $item = (array)$item;

        if (empty($item['created_at'])) {
            $item['created_at'] = date('Y-m-d H:i:s');
            $item['updated_at'] = date('Y-m-d H:i:s');
        }

        $query = $this->db->placehold("
            INSERT INTO __sms_sales SET ?%
        ", $item);
        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }

    public function update_smssales($id, $item)
    {
        $item['updated_at'] = date('Y-m-d H:i:s');

        $query = $this->db->placehold("
            UPDATE __sms_sales SET ?% WHERE id = ?
        ", (array)$item, (int)$id);
        $this->db->query($query);

        return $id;
    }
}
