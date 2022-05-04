<?php

class CompanyChecks extends Core
{
    public function add_company_check($company_id, $data)
    {
        $this->db->query('
        INSERT INTO s_company_checks
        SET company_id = ?, data = ?
        ', $company_id, $data);
        return $this->db->insert_id();
    }

    public function get_five_company_checks($company_id)
    {
        $this->db->query("
        SELECT id, data, created
        FROM s_company_checks
        WHERE company_id = ?
        ORDER BY created DESC
        LIMIT 5
        ", $company_id);
        return $this->db->result();
    }

    public function get_last_company_check($company_id)
    {
        $this->db->query("
        SELECT id, data, created
        FROM s_company_checks
        WHERE company_id = ?
        ORDER BY created DESC
        LIMIT 1
        ", $company_id);
        return $this->db->result();
    }
}