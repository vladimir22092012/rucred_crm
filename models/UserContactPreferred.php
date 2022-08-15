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

    public function add($preferred)
    {
        $query = $this->db->placehold("
            INSERT INTO s_user_contact_preferred 
            SET ?%
        ", $preferred);
        $this->db->query($query);
    }
}