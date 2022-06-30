<?php

class CompanyChecks extends Core
{
    public function add($client)
    {
        $query = $this->db->placehold("
        INSERT INTO s_company_checks
        SET ?%
        ", $client);

        $this->db->query($query);
        return $this->db->insert_id();
    }

    public function get_five_company_checks($company_id)
    {
        $this->db->query("
        SELECT id, data, type, created
        FROM s_company_checks
        WHERE company_id = ?
        ORDER BY created DESC
        LIMIT 5
        ", $company_id);
        return $this->db->results();
    }

    public function get_last_company_check($company_id)
    {
        $this->db->query("
        SELECT id, data, type, created
        FROM s_company_checks
        WHERE company_id = ?
        ORDER BY created DESC
        LIMIT 1
        ", $company_id);
        return $this->db->result();
    }
}
