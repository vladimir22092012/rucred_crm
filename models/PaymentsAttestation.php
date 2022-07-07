<?php

class PaymentsAttestation extends Core
{
    public function add($client)
    {
        $query = $this->db->placehold("
        INSERT INTO s_payments_attestation
        SET ?%
        ", $client);

        $this->db->query($query);
        return $this->db->insert_id();
    }

    public function get($fio)
    {
        $query = $this->db->placehold("
        SELECT *
        FROM s_payments_attestation
        WHERE fio = ?
        ", $fio);

        $this->db->query($query);
        $result = $this->db->result();

        return $result;
    }
}