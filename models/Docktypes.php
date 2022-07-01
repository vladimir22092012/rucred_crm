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

    public function get_templates($filter = array())
    {
        $id_filter = '';

        if(isset($filter['id']))
            $id_filter = $this->db->placehold("AND id IN (?@)", (array)$filter['id']);

        $query = $this->db->placehold("
        SELECT templates
        FROM s_dock_types
        WHERE 1
        $id_filter
        ");

        $this->db->query($query);
        $templates = $this->db->results();
        return $templates;
    }
}