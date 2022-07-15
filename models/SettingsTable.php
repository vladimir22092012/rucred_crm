<?php

class SettingsTable extends Core
{
    public function gets(){

        $query = $this->db->placehold("
        SELECT *
        FROM s_settings
        ");

        $this->db->query($query);
        $results = $this->db->results();
        return $results;
    }
    public function update($name , $value){
        $query = $this->db->placehold("
        UPDATE s_settings
        SET `value` = ?
        WHERE `name` = ?
        ",$value, $name);

        $this->db->query($query);
    }
}