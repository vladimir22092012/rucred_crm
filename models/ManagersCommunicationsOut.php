<?php

class ManagersCommunicationsOut extends Core
{
    public function add($permission)
    {
        $query = $this->db->placehold("
        INSERT INTO s_managers_communications_out
        SET ?%
        ", $permission);

        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }

    public function update($id, $permission)
    {
        $query = $this->db->placehold("
        UPDATE s_managers_communications_out
        SET ?%
        WHERE theme_id = ?
        ", $permission, $id);

        $this->db->query($query);
    }

    public function get($theme_id){

        $query = $this->db->placehold("
        SELECT * 
        FROM s_managers_communications_out
        where theme_id = ?
        ", $theme_id);

        $this->db->query($query);
        $result = $this->db->results();

        return $result;
    }

    public function delete($id)
    {
        $query = $this->db->placehold("
        DELETE FROM s_managers_communications_out
        WHERE theme_id = ?
        ", $id);

        $this->db->query($query);
    }
}