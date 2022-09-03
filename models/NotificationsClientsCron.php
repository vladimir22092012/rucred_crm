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
}