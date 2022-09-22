<?php

class PaymentsToSchedules extends Core
{
    public function add($payments)
    {
        $query = $this->db->placehold("
        INSERT INTO s_payments_to_schedules
        SET ?%
        ", $payments);

        $this->db->query($query);
        return $this->db->insert_id();
    }
}