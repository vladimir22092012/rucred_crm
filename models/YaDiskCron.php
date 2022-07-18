<?php

class YaDiskCron extends Core
{
    public function add($cron){

        $query = $this->db->placehold("
        INSERT INTO s_yadisk_cron
        SET ?%
        ", $cron);

        $this->db->query($query);
    }

    public function gets(){

        $query = $this->db->placehold("
        SELECT *
        FROM s_yadisk_cron
        WHERE is_complited = 0
        ");

        $this->db->query($query);
        $results = $this->db->results();

        return $results;
    }

    public function update($cron, $id){
        $query = $this->db->placehold("
        UPDATE s_yadisk_cron
        SET ?%
        WHERE id = ?
        ", $cron, $id);

        $this->db->query($query);
    }
}