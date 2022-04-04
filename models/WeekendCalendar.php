<?php

class WeekendCalendar extends Core

{
    public function check_date($date)
    {
        $query = $this->db->placehold("
        SELECT *
        FROM s_weekend_calendar
        WHERE `date` = ?
        ", $date);

        $this->db->query($query);

        $result = $this->db->result();

        return $result;
    }
}