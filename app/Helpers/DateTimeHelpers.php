<?php
namespace App\Helpers;

/** Базовый класс хелпер для работы с датами */
class DateTimeHelpers {

    public static function getDatesArray($startTime, $endTime) {
        $day = 86400;
        $format = 'Y-m-d';
        $startTime = strtotime($startTime);
        $endTime = strtotime($endTime);
        $numDays = round(($endTime - $startTime) / $day);
        $days = [];
        for ($i = 0; $i < $numDays + 1; $i++) {
            $days[] = date($format, ($startTime + ($i * $day)));
        }
        return $days;
    }

}
?>
