<?php

class OrganisationSettlements extends Core
{
    public function get_settlements()
    {
        $query = $this->db->placehold("
        SELECT *
        FROM s_organization_settlements
        WHERE is_deleted != 1
        ");

        $this->db->query($query);

        $results = $this->db->results();

        return $results;
    }

    public function get_settlement($id)
    {
        $query = $this->db->placehold("
        SELECT *
        FROM s_organization_settlements
        WHERE id = ?
        ", (int)$id);

        $this->db->query($query);

        $results = $this->db->result();

        return $results;
    }

    public function add_settlements($settlement)
    {
        $query = $this->db->placehold("
        INSERT INTO s_organization_settlements
        SET ?%
        ", (array)$settlement);

        $this->db->query($query);
    }

    public function update_settlements($id, $settlement)
    {
        $query = $this->db->placehold("
        UPDATE s_organization_settlements
        SET ?%
        WHERE id = ?
        ", (array)$settlement, (int)$id);

        $this->db->query($query);
    }

    public function delete_settlements($id)
    {
        $query = $this->db->placehold("
        UPDATE s_organization_settlements
        SET is_deleted = 1
        WHERE id = ?
        ", (int)$id);

        $this->db->query($query);
    }

    public function change_std_flag($id)
    {
        $query = $this->db->placehold("
        UPDATE s_organization_settlements
        SET std = 0
        WHERE std = 1
        ");

        $this->db->query($query);

        $query = $this->db->placehold("
        UPDATE s_organization_settlements
        SET std = 1
        WHERE id = ?
        ", (int)$id);

        $this->db->query($query);
    }
}