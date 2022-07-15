<?php

class AspCodes extends Core
{
    public function add_code($code)
    {
        $uid = rand(000000000, 999999999);
        $code['uid'] = $uid;

        $query = $this->db->placehold("
        INSERT INTO s_asp_codes
        SET ?%
        ", (array)$code);
        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }

    public function get_code($param = null)
    {
        $where = '';

        if (isset($param['id']))
            $where = $this->db->placehold("WHERE id = ?", $param['id']);

        if (isset($param['code']))
            $where = $this->db->placehold("WHERE code = ?", $param['code']);

        $query = $this->db->placehold("
        SELECT * 
        FROM s_asp_codes
        $where
        ");

        $this->db->query($query);
        $code = $this->db->result();

        return $code;
    }

    public function get_codes($params = [])
    {
        $sort = '';
        $order = '';

        if(isset($params['sort']))
            $sort = $this->db->placehold('ORDER BY '.$params['sort']);

        if(isset($params['order_id']))
            $order = $this->db->placehold("AND order_id = ?", $params['order_id']);

        $query = $this->db->placehold("
        SELECT * 
        FROM s_asp_codes
        WHERE 1
        $order
        $sort
        ");

        $this->db->query($query);
        $codes = $this->db->results();

        return $codes;
    }
}