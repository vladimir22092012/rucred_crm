<?php

class WeekendCalendarORM extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 's_weekend_calendar';
    protected $guarded = [];
    public $timestamps = false;

    public static function checkDate($date) {

        $date = self::select('id', 'date')
            ->where('date', $date)
            ->first();

        return $date;
    }
}