<?php

class Tickets extends Core
{
    public $status =
        [
            0 => 'Новая заявка',
            1 => 'Направленный тикет',
            2 => 'Принят',
            3 => 'На проверку',
            4 => 'Исполнено',
            5 => 'На доработку',
            6 => 'Закрыт'
        ];

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
        $manager = '';
        $status = $this->db->placehold("AND status != 6");
        $executor = '';

        if ($in_out == 'out') {
            $out = $this->db->placehold("AND creator = ?", $manager_id);
        }

        if ($manager_role == 'employer' && $in_out == 'in') {
            $employer = $this->managers->get_manager($manager_id);
            $out = $this->db->placehold("AND group_id = ?", $employer->group_id);
        }

        if ($in_out == 'in' && in_array($manager_role, ['underwriter', 'middle'])) {
            $out = $this->db->placehold("AND company_id = 2");
            $manager = $this->db->placehold("AND creator != ?", $manager_id);
        }

        if ($in_out == 'archive') {
            $status = $this->db->placehold("AND status = 6");
            $executor = $this->db->placehold("AND executor = ?", $manager_id);
        }

        $query = $this->db->placehold("
        SELECT *
        FROM s_tickets
        WHERE 1
        $out
        $manager
        $status
        $executor
        ");

        $this->db->query($query);
        $tickets = $this->db->results();

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

    public function update_ticket($id, $ticket)
    {

        $query = $this->db->placehold("
        UPDATE s_tickets
        SET ?%
        WHERE id = ?
        ", $ticket, $id);

        return $this->db->query($query);
    }

    public function close_neworder_ticket($order_id)
    {

        $query = $this->db->placehold("
        UPDATE s_tickets
        SET status = 6
        WHERE order_id = ?
        ", $order_id);

        $this->db->query($query);
    }
}