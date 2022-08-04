<?php

class AspCodes extends Core
{
    public function add_code($code)
    {
        $code['uid'] = $this->uniq_id();

        $query = $this->db->placehold("
        INSERT INTO s_asp_codes
        SET ?%
        ", (array)$code);
        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }

    public function uniq_id(){

        $uid = rand(000000000, 999999999);

        $query = $this->db->placehold("
        SELECT * 
        FROM s_asp_codes
        WHERE uid = ?
        ", $uid);

        $this->db->query($query);
        $code = $this->db->result();

        if(!empty($code))
            $this->uniq_id();
        else
            return $uid;
    }

    public function get_code($param = array())
    {
        $where = '';

        if (isset($param['id']))
            $where .= $this->db->placehold("AND id = ? ", $param['id']);

        if (isset($param['code']))
            $where .= $this->db->placehold("AND code = ? ", $param['code']);

        if (isset($param['order_id']))
            $where .= $this->db->placehold("AND order_id = ? ", $param['order_id']);

        if (isset($param['type']))
            $where .= $this->db->placehold("AND `type` = ? ", $param['type']);

        $query = $this->db->placehold("
        SELECT * 
        FROM s_asp_codes
        WHERE 1
        $where
        ORDER BY id DESC
        LIMIT 1
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