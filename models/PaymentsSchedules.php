<?php

class PaymentsSchedules extends Core
{
    public function add($schedule)
    {
        $query = $this->db->placehold("
        INSERT INTO s_payments_schedules
        SET ?%
        ", $schedule);

        $this->db->query($query);
        return $this->db->insert_id();
    }

    public function get($filter)
    {
        $actual_flag = '';
        $order_filter = '';

        if(isset($filter['actual']))
            $actual_flag = $this->db->placehold("AND actual = ?", $filter['actual']);

        if(isset($filter['order_id']))
            $order_filter = $this->db->placehold("AND order_id = ?", $filter['order_id']);

        $query = $this->db->placehold("
        SELECT * 
        FROM s_payments_schedules
        WHERE 1
        $actual_flag
        $order_filter
        ");

        $this->db->query($query);
        return $this->db->result();
    }

    public function gets($order_id)
    {
        $query = $this->db->placehold("
        SELECT * 
        FROM s_payments_schedules
        WHERE order_id = ?
        ", $order_id);

        $this->db->query($query);
        return $this->db->results();
    }

    public function update($schedule_id, $schedule)
    {
        $query = $this->db->placehold("
        UPDATE s_payments_schedules
        SET ?%
        WHERE id = ?
        ", $schedule, $schedule_id);

        $this->db->query($query);
    }

    public function updates($order_id, $schedule)
    {
        $query = $this->db->placehold("
        UPDATE s_payments_schedules
        SET ?%
        WHERE order_id = ?
        ", $schedule, $order_id);

        $this->db->query($query);
    }

    public function delete($id)
    {

    }
}