<?php

class CommunicationsThemes extends Core
{
    public function add($theme)
    {
        $query = $this->db->placehold("
        INSERT INTO s_communications_themes
        SET ?%
        ", $theme);

        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }
    public function get($id)
    {
        $query = $this->db->placehold("
        SELECT *
        FROM s_communications_themes
        WHERE id = ?
        ", $id);

        $this->db->query($query);
        $theme = $this->db->result();

        return $theme;
    }
    public function gets($filter = array())
    {
        $name_filter = '';
        $number_filter = '';
        $unique_filter = '';
        $create_ticket = '';

        if(isset($filter['sort']))
            $sort = $filter['sort'];
        else
            $sort = 'id asc';

        if(isset($filter['name']))
            $name_filter = $this->db->placehold("AND name = ?", $filter['name']);

        if(isset($filter['create_ticket']))
            $create_ticket = $this->db->placehold("AND id in (?@)", $filter['create_ticket']);

        if(isset($filter['number']))
            $name_filter = $this->db->placehold("AND number = ?", $filter['number']);

        if(isset($filter['id']))
            $unique_filter = $this->db->placehold("AND id != ?", $filter['id']);

        $query = $this->db->placehold("
        SELECT *
        FROM s_communications_themes
        WHERE 1
        $name_filter
        $number_filter
        $unique_filter
        $create_ticket
        ORDER BY $sort
        ");

        $this->db->query($query);
        $themes = $this->db->results();

        return $themes;
    }

    public function update($id, $theme)
    {
        $query = $this->db->placehold("
        UPDATE s_communications_themes
        SET ?%
        WHERE id = ?
        ", $theme, $id);

        $this->db->query($query);
    }
    public function delete($id)
    {
        $query = $this->db->placehold("
        DELETE FROM s_communications_themes
        WHERE id = ?
        ",$id);

        $this->db->query($query);
    }
}
