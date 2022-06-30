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

    public function get($fio){

        $query = $this->db->placehold("
        SELECT *
        FROM s_company_checks
        WHERE fio = ?
        ", $fio);

        $this->db->query($query);
        $results = $this->db->result();

        return $results;
    }
}
