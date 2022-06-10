<?php

class DocksPermissions extends Core

{
    public function add_permission($permission)
    {
        $query = $this->db->placehold("
        INSERT INTO s_docks_permissions
        SET ?%
        ", $permission);

        $this->db->query($query);
    }

    public function delete_permission($permission)
    {
        $query = $this->db->placehold("
        DELETE FROM s_docks_permissions
        WHERE role_id = ?
        AND docktype_id = ?
        ", $permission['role_id'], $permission['docktype_id']);

        $this->db->query($query);
    }

    public function get_permissions()
    {
        $query = $this->db->placehold("
        SELECT * 
        FROM s_docks_permissions
        ");

        $this->db->query($query);
        $permissions = $this->db->results();
        return $permissions;
    }
}