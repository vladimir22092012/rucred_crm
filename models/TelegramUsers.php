<?php

class TelegramUsers extends Core
{
    public function add_user($user)
    {
        $query = $this->db->placehold("
        INSERT INTO s_telegram_users
        SET ?%
        ", (array)$user);

        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }
}