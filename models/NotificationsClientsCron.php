<?php

class NotificationsClientsCron extends Core
{
    public function gets()
    {
        $query = $this->db->placehold("
        SELECT *
        FROM s_notifications_clients_cron
        WHERE is_complited = 0
        ");

        $this->db->query($query);

        $results = $this->db->results();

        return $results;
    }

    public function add($cron)
    {
        $query = $this->db->placehold("
        INSERT INTO s_notifications_clients_cron
        SET ?%
        ", $cron);

        $this->db->query($query);
    }
}