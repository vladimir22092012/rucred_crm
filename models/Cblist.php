<?php

class Cblist extends Core
{
    public function add_person($person)
    {
        $query = $this->db->placehold("
            INSERT INTO __cblist SET ?%
        ", (array)$person);
        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }

    public function update_person($id, $person)
    {
        $query = $this->db->placehold("
            UPDATE __cblist SET ?% WHERE id = ?
        ", (array)$person, (int)$id);
        $this->db->query($query);

        return $id;
    }

    public function delete_person($id)
    {
        $query = $this->db->placehold("
            DELETE FROM __cblist WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
    }

    public function search($fio)
    {
        $query = $this->db->placehold("
            SELECT id
            FROM __cblist
            WHERE fio = ?
        ", $fio);
        $this->db->query($query);

        return $this->db->result('id');
    }
}
