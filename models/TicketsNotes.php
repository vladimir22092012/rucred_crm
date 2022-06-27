<?php

class TicketsNotes extends Core
{
    public function add($note)
    {
        $query = $this->db->placehold("
        INSERT INTO s_tickets_notifications
        SET ?%
        ", $note);

        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }

    public function delete($ticket_id)
    {
        $query = $this->db->placehold("
        DELETE FROM s_tickets_notifications
        WHERE ticket_id = ?
        ", $ticket_id);

        $this->db->query($query);
    }

    public function get($ticket_id, $user_id)
    {
        $query = $this->db->placehold("
        INSERT INTO s_tickets_notifications
        WHERE ticket_id = ?
        AND user_id = ?
        ", $ticket_id, $user_id);

        $this->db->query($query);
        $id = $this->db->result();

        return $id;

    }
}