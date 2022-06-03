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

    public function get_docs($message_id)
    {
        $query = $this->db->placehold("
            SELECT *
            FROM s_tickets_docs
            WHERE message_id = ?
            ", (int)$message_id);

        $this->db->query($query);
        $docs = $this->db->results();

        return $docs;
    }
}