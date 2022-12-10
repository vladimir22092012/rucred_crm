<?php

class TestController extends Controller
{
    public function fetch()
    {
        $paydate = $this->check_pay_date(new DateTime(date('2022-12-11')));

        var_dump($paydate);
        exit;
    }

    private function check_pay_date($date)
    {
        $checkDate = WeekendCalendarORM::where('date', $date->format('Y-m-d'))->first();

        if (!empty($checkDate)) {
            $date->sub(new DateInterval('P1D'));
            $this->check_pay_date($date);
        }

        return $date;
    }
}