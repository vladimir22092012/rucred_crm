<?php

class NotificationsCron extends Core
{
    public function add($cron){
        $query = $this->db->placehold("
        INSERT INTO s_notifications_cron
        SET ?%
        ", $cron);

        $this->db->query($query);
    }
    public function get($id){

        $query = $this->db->placehold("
        SELECT *
        FROM s_notifications_cron
        WHERE id = ?
        ", $id);

        $this->db->query($query);
        $result = $this->db->result();

        return $result;

    }
    public function gets(){

        $query = $this->db->placehold("
        SELECT *
        FROM s_notifications_cron
        WHERE is_complited = 0
        ");

        var_dump($query);
        exit;

        $this->db->query($query);

        $results = $this->db->results();

        return $results;

    }
    public function update($id, $cron){
        $query = $this->db->placehold("
        UPDATE s_notifications_cron
        SET ?%
        WHERE id = ?
        ", $cron, $id);

        $this->db->query($query);
    }
    public function delete($id){
        $query = $this->db->placehold("
        DELETE FROM s_notifications_cron
        WHERE id = ?
        ", $id);

        $this->db->query($query);
    }
}