<?php

class GroupLoanTypes extends Core
{
    public function get_loantype_groups($loantype_id)
    {
        $query = $this->db->placehold("
        SELECT * 
        FROM s_group_loantypes
        WHERE loantype_id = ?
        ", $loantype_id);

        $this->db->query($query);
        $results = $this->db->results();

        $groups = [];

        foreach ($results as $result) {
            $group = $this->Groups->get_group($result->group_id);
            $groups[] =
                [
                    'id' => $group->id,
                    'name' => $group->name,
                    'standart_percents' => $result->standart_percents,
                    'preferential_percents' => $result->preferential_percents
                ];
        }

        return $groups;
    }

    public function add_group($group)
    {
        $query = $this->db->placehold("
        INSERT INTO s_group_loantypes SET ?%", $group);

        $this->db->query($query);

        $id = $this->db->insert_id();

        return $id;
    }

    public function update_record($record)
    {
        $query = $this->db->placehold("
        UPDATE s_group_loantypes 
        SET standart_percents = ?, preferential_percents = ?
        WHERE group_id = ?
        AND loantype_id = ? 
        ", (float)$record['standart_percents'],
            (float)$record['preferential_percents'],
            $record['group_id'],
            $record['loantype_id']);

        $this->db->query($query);
    }
}