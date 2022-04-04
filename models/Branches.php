<?php

class Branches extends Core
{
    public function add_branch($branch)
    {
        $query = $this->db->placehold("
        INSERT INTO s_branches
        SET ?%
        ", $branch);

        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }

    public function get_company_branches($company_id)
    {
        $query = $this->db->placehold("
        SELECT * 
        FROM s_branches
        WHERE company_id = ?
        ", $company_id);

        $this->db->query($query);
        $results = $this->db->results();

        return $results;
    }

    public function get_branches()
    {
        $query = $this->db->placehold("
        SELECT * 
        FROM s_branches
        ");

        $this->db->query($query);
        $results = $this->db->results();

        return $results;
    }

    public function last_id()
    {
        $query = $this->db->placehold("
        SELECT MAX(`id`) as id
        FROM s_branches
        ");

        $this->db->query($query);
        $id = $this->db->result('id');
        return $id;
    }
}