<?php

class ManagerRoles extends Core
{
    public function get()
    {
        $query = $this->db->placehold("
        SELECT *
        FROM s_managers_roles
        ");

        $this->db->query($query);
        $roles = $this->db->results();

        return $roles;
    }

    public function gets($name)
    {
        $query = $this->db->placehold("
        SELECT id
        FROM s_managers_roles
        WHERE `name` = ?
        ", $name);

        $this->db->query($query);
        $id = $this->db->result('id');

        return $id;
    }
}