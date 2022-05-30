<?php

class AspCodes extends Core
{
    public function add_code($code)
    {
        $query = $this->db->placehold("
        INSERT INTO s_asp_codes
        SET ?%
        ", $code);

        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }

    public function get_code($id)
    {
        $query = $this->db->placehold("
        SELECT * 
        FROM s_asp_codes
        WHERE id = ?
        ", $id);

        $this->db->query($query);
        $code = $this->db->result();

        return $code;
    }
}