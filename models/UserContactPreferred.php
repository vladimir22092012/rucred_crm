<?php

class UserContactPreferred extends Core
{
    public function gets()
    {
        $query = $this->db->placehold("
        SELECT * 
        FROM s_user_contact_preferred
        ");

        $this->db->query($query);
        $results = $this->db->results();

        return $results;
    }

    public function get($user_id)
    {
        $query = $this->db->placehold("
        SELECT * 
        FROM s_user_contact_preferred
        WHERE user_id = ?
        ", $user_id);

        $this->db->query($query);
        $results = $this->db->results();

        return $results;
    }

    public function add($preferred)
    {
        $query = $this->db->placehold("
            INSERT INTO s_user_contact_preferred 
            SET ?%
        ", $preferred);
        $this->db->query($query);
    }

    public function delete($user_id)
    {
        $query = $this->db->placehold("
            DELETE FROM s_user_contact_preferred 
            Where user_id = ?
        ", $user_id);
        $this->db->query($query);
    }
}