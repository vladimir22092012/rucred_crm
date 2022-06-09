<?php

class ManagerRoles extends Core
{
    public function get_roles()
    {
        $query = $this->db->placehold("
        SELECT *
        FROM s_managers_roles
        ");

        $this->db->query($query);
        $roles = $this->db->results();

        return $roles;
    }
}