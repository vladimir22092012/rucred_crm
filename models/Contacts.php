<?php

class Contacts extends Core
{
    public function get_contacts($user_id)
    {
        $query = $this->db->placehold("
        SELECT * 
        FROM s_contacts
        WHERE user_id = ?
        ", $user_id);

        $this->db->query($query);
        $results = $this->db->results();

        return $results;
    }

    public function delete($user_id)
    {
        $query = $this->db->placehold("
        DELETE FROM s_contacts
        WHERE user_id = ?
        ", $user_id);

        $this->db->query($query);
    }

    public function add($contact)
    {
        $query = $this->db->placehold("
            INSERT INTO s_contacts SET ?%
        ", $contact);
        $this->db->query($query);
        return $this->db->insert_id();
    }
}