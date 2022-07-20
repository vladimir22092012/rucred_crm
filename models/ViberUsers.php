<?php

class ViberUsers extends Core
{
    public function add($user)
    {
        $query = $this->db->placehold("
        INSERT INTO s_viber_users
        SET ?%
        ", (array)$user);

        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }

    public function update($user, $user_id)
    {
        $query = $this->db->placehold("
        UPDATE s_viber_users
        SET ?%
        WHERE user_id = ?
        ", $user, $user_id);

        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }

    public function get($user_id, $manager_flag = false)
    {
        if ($manager_flag)
            $this->db->placehold("AND is_manager = ?", $manager_flag);

        $query = $this->db->placehold("
        SELECT * 
        FROM s_viber_users
        WHERE user_id = ?
        $manager_flag
        ", $user_id);

        $this->db->query($query);
        $user = $this->db->result();

        return $user;
    }

    public function get_user_by_chat_id($chat_id)
    {
        $query = $this->db->placehold("
        SELECT * 
        FROM s_viber_users
        WHERE chat_id = ?
        ", $chat_id);

        $this->db->query($query);
        $user = $this->db->result();

        return $user;
    }
}