<?php

class TicketsDocs extends Core
{
    public function add_doc($doc)
    {
        $query = $this->db->placehold("
        INSERT INTO s_tickets_docs
        SET ?%
        ", $doc);

        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }
}