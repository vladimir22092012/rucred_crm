<?php

class Payments extends Core
{
    public function add($payment)
    {
        $query = $this->db->placehold("
        INSERT INTO s_payments
        SET ?%
        ", $payment);

        $this->db->query($query);
        return $this->db->insert_id();
    }

    public function gets()
    {
        $query = $this->db->placehold("
        SELECT * 
        FROM s_payments");

        $this->db->query($query);
        return $this->db->results();
    }
}