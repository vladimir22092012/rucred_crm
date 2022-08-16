<?php

class TelegramUsers extends Core
{
    public function add($user)
    {
        $query = $this->db->placehold("
        INSERT INTO s_telegram_users
        SET ?%
        ", (array)$user);

        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }

    public function update($user)
    {
        $query = $this->db->placehold("
        UPDATE s_telegram_users
        SET chat_id = ?
        WHERE token = ?
        ", $user['chat_id'], $user['token']);

        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }

    public function get($user_id, $manager_flag = false)
    {
        if($manager_flag)
            $manager_flag = $this->db->placehold("AND is_manager = ?", $manager_flag);

        $query = $this->db->placehold("
        SELECT * 
        FROM s_telegram_users
        WHERE user_id = ?
        $manager_flag
        ", $user_id);

        var_dump($query);

        $this->db->query($query);
        $user = $this->db->result();

        return $user;
    }
}