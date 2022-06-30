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
}