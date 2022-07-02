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
}