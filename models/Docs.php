<?php

error_reporting(-1);
ini_set('display_errors', 'On');
class Docs extends Core
{
    public function add_doc($document)
    {

        $qeury = $this->db->placehold("
        INSERT INTO s_docs
        SET ?%
        ", $document);

        $this->db->query($qeury);
    }

    public function get_docs($company_id)
    {
        $qeury = $this->db->placehold("
        SELECT * 
        FROM s_docs
        WHERE company_id = ?
        ", (int)$company_id);

        $this->db->query($qeury);
        $result = $this->db->results();

        return $result;
    }
}
