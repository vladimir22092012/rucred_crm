<?php

class Groups extends Core
{
    public function get_groups($filter = array())
    {
        $employer_filter = '';

        if(isset($filter['employer']))
            $employer_filter = $this->db->placehold("AND id = ?", (int)$filter['employer']);

        $query = $this->db->placehold("
        SELECT *
        FROM s_groups
        WHERE 1
        $employer_filter
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

    public function delete_group($id)
    {
        $query = $this->db->placehold("
        DELETE FROM s_groups 
        WHERE id = ?
        ", $id);

        $this->db->query($query);
    }

    public function last_number()
    {
        $query = $this->db->placehold("
        SELECT `number`
        FROM s_groups
        order by id desc limit 1
        ");

        $this->db->query($query);
        $id = $this->db->result('number');
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
