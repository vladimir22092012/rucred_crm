<?php

class Companies extends Core
{
    public function add_company($company)
    {
        $query = $this->db->placehold("
        INSERT INTO s_companies
        SET ?%
        ", $company);

        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }

    public function delete_companies($group_id)
    {
        $query = $this->db->placehold("
        DELETE FROM s_companies
        WHERE group_id = ?
        ", $group_id);

        $this->db->query($query);
    }

    public function delete_company($company_id)
    {
        $query = $this->db->placehold("
        DELETE FROM s_companies
        WHERE id = ?
        ", $company_id);

        $this->db->query($query);
    }

    public function get_companies($filter = array())
    {
        $group_id = '';

        if($filter['group_id'])
            $group_id = $this->db->placehold("AND group_id = ?", (int)$filter['group_id']);

        $query = $this->db->placehold("
        SELECT * 
        FROM s_companies
        WHERE 1
        $group_id
        ");

        $this->db->query($query);
        $results = $this->db->results();

        return $results;
    }

    public function get_company($id)
    {
        $query = $this->db->placehold("
        SELECT * 
        FROM s_companies
        WHERE id = ?
        ", $id);

        $this->db->query($query);
        $results = $this->db->result();

        return $results;
    }

    public function update_company($id, $company)
    {
        $query = $this->db->placehold("
        UPDATE s_companies
        SET ?%
        WHERE id = ?
        ", $company, $id);

        $this->db->query($query);
    }

    public function get_company_group($id)
    {
        $query = $this->db->placehold("
        SELECT gr.number as gr_number,
        com.number as com_number,
        gr.name as gr_name,
        com.name as com_name,
        com.eio_position,
        com.eio_fio,
        com.id as com_id,
        gr.id as gr_id,
        com.inn,
        com.ogrn,
        com.kpp,
        com.jur_address,
        com.phys_address,
        com.blocked  
        FROM s_companies as com
        JOIN s_groups as gr on com.group_id = gr.id
        WHERE com.id = ?
        ", $id);

        $this->db->query($query);
        $result = $this->db->result();

        return $result;
    }

    public function get_companies_groups()
    {
        $query = $this->db->placehold("
        SELECT gr.number as gr_number,
        com.number as com_number,
        gr.name as gr_name,
        com.name as com_name,
        com.eio_position,
        com.eio_fio,
        com.id,
        com.inn,
        com.ogrn,
        com.kpp,
        com.jur_address,
        com.phys_address,
        com.blocked
        FROM s_companies as com
        JOIN s_groups as gr on com.group_id = gr.id
        ");

        $this->db->query($query);
        $results = $this->db->results();

        return $results;
    }

    public function last_number($group_id)
    {
        $query = $this->db->placehold("
        SELECT `number`
        FROM s_companies
        where group_id = ?
        order by id desc limit 1
        ", $group_id);

        $this->db->query($query);
        $id = $this->db->result('number');
        return $id;
    }
}