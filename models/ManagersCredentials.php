<?php

class ManagersCredentials extends Core
{
    public function add($credentials){
        $query = $this->db->placehold("
            INSERT INTO s_managers_credentials 
            SET ?%
        ",$credentials);
        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }
    public function get($manager_id){
        $query = $this->db->placehold("
            SELECT * 
            FROM s_managers_credentials 
            WHERE manager_id = ?
        ",$manager_id);
        $this->db->query($query);
        $result = $this->db->result();

        return $result;
    }
    public function gets($filter){

        $manager_filter = '';

        if(isset($filter['manager_id']))
            $manager_filter = $this->db->placehold("AND manager_id = ?", $filter['manager_id']);

        $query = $this->db->placehold("
            SELECT * 
            FROM s_managers_credentials 
            WHERE 1
            $manager_filter
        ");

        $this->db->query($query);
        $result = $this->db->results();

        return $result;
    }
    public function update($credentials){
        $query = $this->db->placehold("
            UPDATE s_managers_credentials 
            SET ?%
        ", $credentials);
        $this->db->query($query);
    }
    public function delete($manager_id){
        $query = $this->db->placehold("
            DELETE FROM s_managers_credentials 
            WHERE manager_id = ?
        ", $manager_id);
        $this->db->query($query);
    }
}