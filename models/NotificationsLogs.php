<?php

class NotificationsLogs extends Core
{
    public function add($log)
    {
        $query = $this->db->placehold("
        INSERT INTO s_notifications_logs
        SET ?%
        ", $log);

        $this->db->query($query);
    }

    public function get($id)
    {
        $query = $this->db->placehold("
        SELECT * 
        FROM s_notifications_logs
        WHERE id = ?
        ", $id);

        $this->db->query($query);

        $log = $this->db->result();

        return $log;
    }

    public function gets()
    {
        $query = $this->db->placehold("
        SELECT * 
        FROM s_notifications_logs");

        $this->db->query($query);

        $logs = $this->db->results();

        return $logs;
    }

    public function update($id, $log)
    {
        $query = $this->db->placehold("
        UPDATE s_notifications_logs
        SET ?%
        WHERE id = ?
        ", $log, $id);

        $this->db->query($query);
    }

    public function delete($id)
    {
        $query = $this->db->placehold("
        DELETE FROM s_notifications_logs
        WHERE id = ?
        ", $id);

        $this->db->query($query);
    }
}