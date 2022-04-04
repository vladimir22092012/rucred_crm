<?php

class Groups extends Core
{
    public function get_groups()
    {
        $query = $this->db->placehold("
        SELECT *
        FROM s_groups
        ");

        $this->db->query($query);

        $result = $this->db->results();

        return $result;
    }

    public function add_group($group)
    {
        $query = $this->db->placehold("
        INSERT INTO s_groups SET ?%
        ", $group);

        $this->db->query($query);
    }

    public function last_id()
    {
        $query = $this->db->placehold("
        SELECT MAX(`id`) as id
        FROM s_groups
        ");

        $this->db->query($query);
        $id = $this->db->result('id');
        return $id;
    }
}