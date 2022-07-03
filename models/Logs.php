<?php

class Logs extends Core
{
    public function add($log)
    {
        $query = $this->db->placehold("
        INSERT INTO s_logs
        SET ?%
        ", (array)$log);

        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }
}