<?php

class Tickets extends Core
{
    protected $status =
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

    public function get_tickets($manager_role, $manager_id, $in_out, $sort = 't.id asc')
    {
        $out = '';
        $manager = '';
        $theme = '';
        $status = $this->db->placehold("AND t.status != 6");
        $executor = '';
        $creator = '';


        if(isset($sort['sort']))
            $sort = $sort['sort'];

        if ($in_out == 'out') {
            $out = $this->db->placehold("AND t.creator = ?", $manager_id);
        }

        if ($in_out == 'in') {
            $creator = $this->db->placehold("AND t.creator != ?", $manager_id);
            $out = $this->db->placehold("AND t.group_id = 2");
            if(in_array($manager_role, ['underwriter', 'middle'])){
                $manager = $this->db->placehold("AND t.creator != ?", $manager_id);

                if($manager_role == 'underwriter')
                    $theme = $this->db->placehold("AND t.theme_id not in (12, 37)");
            }
        }

        if ($manager_role == 'employer' && $in_out == 'in') {
            $employer = $this->managers->get_manager($manager_id);
            $out = $this->db->placehold("AND t.group_id = ?", $employer->group_id);
        }

        if ($in_out == 'archive') {
            $status = $this->db->placehold("AND t.status = 6");
            $executor = $this->db->placehold("AND t.executor = ?", $manager_id);
        }

        $group_by = explode(' ', $sort);
        $group_by = $group_by[0];

        $query = $this->db->placehold("
        SELECT *
        FROM s_tickets as t
        LEFT JOIN s_tickets_notifications as n on n.ticket_id = t.id
        WHERE 1
        $out
        $manager
        $status
        $executor
        $creator
        $theme
        GROUP BY $group_by
        ORDER BY $sort
        ");

        var_dump($query);
        exit;

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