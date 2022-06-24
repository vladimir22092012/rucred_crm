<?php

class TelegramLogs extends Core
{
    public function add_log($log)
    {
        $query = $this->db->placehold("
        INSERT INTO s_telegram_logs
        SET ?%
        ", (array)$log);

        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }
}