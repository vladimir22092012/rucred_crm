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

    public function update_branch($branch, $id)
    {
        $query = $this->db->placehold("
        UPDATE s_branches
        SET ?%
        WHERE id = ?
        ", $branch, $id);

        $this->db->query($query);
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

    public function get_branches($filter = array())
    {
        $company_id = '';

        if ($filter['company_id']) {
            $company_id = $this->db->placehold("AND company_id = ?", (int)$filter['company_id']);
        }

        $query = $this->db->placehold("
        SELECT * 
        FROM s_branches
        WHERE 1
        $company_id
        ");

        $this->db->query($query);
        $results = $this->db->results();

        return $results;
    }

    public function get_branch($id)
    {
        $query = $this->db->placehold("
        SELECT * 
        FROM s_branches
        WHERE id = ?
        ", $id);

        $this->db->query($query);
        $result = $this->db->result();

        return $result;
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

    public function delete_branche($id)
    {

        $query = $this->db->placehold("
        DELETE FROM s_branches
        where id = ?
        ", (int)$id);

        $this->db->query($query);
    }

    public function delete_branches($array_id)
    {

        $group_id = '';
        $company_id = '';

        if (isset($array_id['group_id'])) {
            $group_id = $this->db->placehold("AND group_id = ?", (int)$array_id['group_id']);
        }

        if (isset($array_id['company_id'])) {
            $company_id = $this->db->placehold("AND company_id = ?", (int)$array_id['company_id']);
        }

        $query = $this->db->placehold("
        DELETE FROM s_branches
        where 1
        $group_id
        $company_id
        ");

        $this->db->query($query);
    }

    public function edit_branch($branch, $branch_id)
    {
        $query = $this->db->placehold("
        UPDATE s_branches
        SET ?%
        WHERE id = ?
        ", $branch, (int)$branch_id);

        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }
}
