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
}