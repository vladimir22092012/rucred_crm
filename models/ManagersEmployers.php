<?php

class ManagersEmployers extends Core

{
    public function add_record($record)
    {
        $query = $this->db->placehold("
            INSERT INTO s_managers_employers SET ?%
        ", (array)$record);
        $this->db->query($query);

    }

    public function delete_records($manager_id)
    {
        $query = $this->db->placehold("
            DELETE FROM s_managers_employers 
            WHERE manager_id = ?
        ", $manager_id);
        $this->db->query($query);
    }

    public function get_records($manager_id)
    {
        $query = $this->db->placehold("
            SELECT *
            FROM s_managers_employers 
            WHERE manager_id = ?
        ", $manager_id);
        $this->db->query($query);
        $records = $this->db->results();

        $managers_company = array();

        foreach ($records as $record) {
            $company = $this->Companies->get_company($record->company_id);
            $managers_company[$company->id] = $company->name;
        }

        return $managers_company;
    }

    public function get_managers_id($company_id)
    {
        $query = $this->db->placehold("
            SELECT manager_id
            FROM s_managers_employers 
            WHERE company_id = ?
        ", (int)$company_id);

        $this->db->query($query);
        $result = $this->db->results();
        return $result;
    }
}