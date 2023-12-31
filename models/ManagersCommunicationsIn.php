<?php

class ManagersCommunicationsIn extends Core
{
    public function add($permission)
    {
        $query = $this->db->placehold("
        INSERT INTO s_managers_communications_in
        SET ?%
        ", $permission);

        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }

    public function update($id, $permission)
    {
        $query = $this->db->placehold("
        UPDATE s_managers_communications_in
        SET ?%
        WHERE theme_id = ?
        ", $permission, $id);

        $this->db->query($query);
    }

    public function get($theme_id){

        $query = $this->db->placehold("
        SELECT * 
        FROM s_managers_communications_in
        where theme_id = ?
        ", $theme_id);

        $this->db->query($query);
        $result = $this->db->results();

        return $result;
    }

    public function gets($role_id){

        $query = $this->db->placehold("
        SELECT theme_id
        FROM s_managers_communications_in
        where role_id = ?
        ", $role_id);

        $this->db->query($query);
        $results = $this->db->results();

        return $results;
    }

    public function delete($id)
    {
        $query = $this->db->placehold("
        DELETE FROM s_managers_communications_in
        WHERE theme_id = ?
        ", $id);

        $this->db->query($query);
    }
}