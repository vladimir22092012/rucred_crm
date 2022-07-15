<?php

class SettingsTable extends Core
{
    public function gets($filter = array()){

        $name_filter = '';

        if(isset($filter['name']))
            $name_filter = $this->db->placehold("AND name = ?", $filter['name']);


        $query = $this->db->placehold("
        SELECT *
        FROM s_settings
        WHERE 1
        $name_filter
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