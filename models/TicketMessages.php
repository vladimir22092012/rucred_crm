<?php


class TicketMessages extends Core

{
    public function add_message($message)
    {
        $query = $this->db->placehold("
        INSERT INTO s_ticket_messages
        SET ?%
        ", (array)$message);

        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }

    public function get_messages($ticket_id)
    {
        $query = $this->db->placehold("
        SELECT * 
        FROM s_ticket_messages
        WHERE ticket_id = ?
        ", $ticket_id);

        $this->db->query($query);
        $messages = $this->db->results();

        foreach ($messages as $message) {

            $message->docs = $this->TicketsDocs->get_docs($message->id);
            $manager = $this->managers->get_manager($message->manager_id);
            $message->manager_name = $manager->name;
        }

        return $messages;
    }
}