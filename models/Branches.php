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

    public function get_branches_by_group($group_id)
    {
        $query = $this->db->placehold("
        SELECT * 
        FROM s_branches
        WHERE group_id = ?
        ", $group_id);

        $this->db->query($query);

        $results = $this->db->results();

        return $results;
    }

    public function last_number($company_id)
    {
        $query = $this->db->placehold("
        SELECT `number`
        FROM s_branches
        where company_id = ?
        order by id desc limit 1
        ", (int)$company_id);

        $this->db->query($query);
        $id = $this->db->result('number');
        return $id;
    }
}