<?php

class Tickets extends Core
{
    public function add_ticket($ticket)
    {
        $query = $this->db->placehold("
        INSERT INTO s_tickets
        SET ?%
        ", $ticket);

        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }

    public function get_tickets($manager_role, $manager_id, $in_out)
    {
        $out = '';

        if ($in_out == 'out')
            $out = $this->db->placehold("AND manager_id = ?", $manager_id);

        if ($manager_role == 'employer') {
            $employer = $this->managers->get_manager($manager_id);
            $out = $this->db->placehold("AND company_id = ?", $employer->company_id);
        }

        $query = $this->db->placehold("
        SELECT *
        FROM s_tickets
        WHERE 1
        $out
        ");

        $this->db->query($query);
        $tickets = $this->db->results();

        $results = array();

        foreach ($tickets as $key => $ticket) {
            $query = $this->db->placehold("
        SELECT *
        FROM s_tickets_docs
        WHERE ticket_id = ?
        ", (int)$ticket->id);

            $this->db->query($query);
            $docs = $this->db->results();

            if (!empty($docs))
                $tickets[$key]->files = 1;

        }

        return $tickets;
    }

    public function get_ticket($id)
    {
        $query = $this->db->placehold("
        SELECT *
        FROM s_tickets
        WHERE id = ?
        ", (int)$id);

        $this->db->query($query);
        $ticket = $this->db->result();

        $query = $this->db->placehold("
        SELECT *
        FROM s_tickets_docs
        WHERE ticket_id = ?
        ", (int)$id);

        $this->db->query($query);
        $ticket->docs = $this->db->results();

        return $ticket;
    }

    public function update_ticket($id, $ticket){

        $query = $this->db->placehold("
        UPDATE s_tickets
        SET ?%
        WHERE id = ?
        ", $ticket, $id);

        return $this->db->query($query);
    }
}