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

    public function get_companies()
    {
        $query = $this->db->placehold("
        SELECT * 
        FROM s_companies
        ");

        $this->db->query($query);
        $results = $this->db->results();

        return $results;
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
        com.phys_address
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
        com.phys_address
        FROM s_companies as com
        JOIN s_groups as gr on com.group_id = gr.id
        ");

        $this->db->query($query);
        $results = $this->db->results();

        return $results;
    }

    public function last_id()
    {
        $query = $this->db->placehold("
        SELECT MAX(`id`) as id
        FROM s_companies
        ");

        $this->db->query($query);
        $id = $this->db->result('id');
        return $id;
    }
}