<?php

class Docktypes extends Core
{
    public function get_docs()
    {
        $query = $this->db->placehold("
        SELECT *
        FROM s_dock_types
        ");

        $this->db->query($query);
        $docs = $this->db->results();

        return $docs;
    }

    public function add_dock($dock)
    {
        $query = $this->db->placehold("
        INSERT INTO s_dock_types
        SET ?%
        ", $dock);

        $this->db->query($query);
        $docs = $this->db->results();

        return $docs;
    }
}