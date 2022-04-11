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

    public function get_group($id)
    {
        $query = $this->db->placehold("
        SELECT *
        FROM s_groups
        WHERE id = ?
        ", $id);

        $this->db->query($query);
        $result = $this->db->result();

        return $result;
    }

    public function add_group($group)
    {
        $query = $this->db->placehold("
        INSERT INTO s_groups SET ?%
        ", $group);

        $this->db->query($query);

        $id = $this->db->insert_id();

        return $id;
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

    public function update_group($id, $group)
    {
        $query = $this->db->placehold("
        UPDATE s_groups 
        SET ?%
        WHERE id = ?
        ", $group, $id);

        $this->db->query($query);
    }
}