<?php

class Scans extends Core
{
    public function add_scan($file)
    {
        $query = $this->db->placehold("
            INSERT INTO __scans SET ?%, created = NOW()
        ", (array)$file);
        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }

    public function get_scans_by_order_id($order_id)
    {
        $query = $this->db->placehold("
            SELECT * 
            FROM __scans 
            WHERE order_id = ?
        ", $order_id);

        $this->db->query($query);

        $results = $this->db->results();

        return $results;
    }

    public function delete_scan($params)
    {
        $query = $this->db->placehold("
            DELETE 
            FROM __scans 
            WHERE order_id = ?
            and `type` = ?
        ", $params['order_id'], $params['type']);

        $this->db->query($query);

        $results = $this->db->results();

        return $results;
    }

    public function delete_all_scans($order_id)
    {
        $query = $this->db->placehold("
            DELETE 
            FROM __scans 
            WHERE order_id = ?
        ", $order_id);

        $this->db->query($query);
    }
}