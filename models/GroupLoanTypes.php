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
                    'record_id' => $result->id,
                    'id' => $group->id,
                    'name' => $group->name,
                    'standart_percents' => $result->standart_percents,
                    'preferential_percents' => $result->preferential_percents,
                    'individual' => $result->individual,
                    'on_off_flag' => $result->on_off_flag
                ];
        }

        return $groups;
    }

    public function gets($params)
    {
        $where = '';

        if(isset($params['group_id']))
            $where .= $this->db->placehold("AND group_id = ?", $params['group_id']);

        if(isset($params['on_off_flag']))
            $where .= $this->db->placehold("AND on_off_flag = ?", $params['on_off_flag']);

        if(isset($params['loantype_id']))
            $where .= $this->db->placehold("AND loantype_id = ?", $params['loantype_id']);

        $query = $this->db->placehold("
        SELECT * 
        FROM s_group_loantypes
        WHERE 1
        $where
        ");

        $this->db->query($query);
        $results = $this->db->results();

        return $results;
    }

    public function get_loantypes_on($group_id, $flag = 2)
    {
        $query = $this->db->placehold("
        SELECT * 
        FROM s_group_loantypes
        WHERE group_id = ?
        and on_off_flag = 1
        ", $group_id);

        $this->db->query($query);
        $results = $this->db->results();

        $loantypes = [];

        foreach ($results as $result) {
            $loantype = $this->Loantypes->get_loantype($result->loantype_id, $flag);

            if(empty($loantype))
                continue;

            $loantypes[] =
                [
                    'max_period' => $loantype->max_period,
                    'id' => $loantype->id,
                    'min_amount' => $loantype->min_amount,
                    'max_amount' => $loantype->max_amount,
                    'name' => $loantype->name,
                    'standart_percents' => $result->standart_percents,
                    'preferential_percents' => $result->preferential_percents,
                    'online_flag' => $loantype->online_flag,
                    'individual' => $result->individual
                ];
        }

        return $loantypes;
    }

    public function add_group($group)
    {
        $query = $this->db->placehold("
        INSERT INTO s_group_loantypes SET ?%", $group);

        $this->db->query($query);

        $id = $this->db->insert_id();

        return $id;
    }

    public function delete_group($id)
    {
        $query = $this->db->placehold("
        DELETE FROM s_group_loantypes 
        WHERE group_id = ?", $id);

        $this->db->query($query);
    }

    public function update_record($record)
    {
        $query = $this->db->placehold(
            "
        UPDATE s_group_loantypes 
        SET standart_percents = ?, preferential_percents = ?, individual = ?
        WHERE group_id = ?
        AND loantype_id = ? 
        ",
            (float)$record['standart_percents'],
            (float)$record['preferential_percents'],
            (float)$record['individual'],
            $record['group_id'],
            $record['loantype_id']
        );

        $this->db->query($query);
    }

    public function change_on_off_flag($record_id, $flag)
    {
        $query = $this->db->placehold("
        UPDATE s_group_loantypes 
        SET on_off_flag = ?
        WHERE id = ?
        ", (int)$flag, (int)$record_id);

        $this->db->query($query);
    }
}
