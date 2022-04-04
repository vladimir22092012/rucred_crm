<?php

error_reporting(-1);
ini_set('display_errors', 'On');

class DocumentController extends Controller
{
    public function fetch()
    {
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($document);echo '</pre><hr />';

        $id = $this->request->get('id');

        $document = $this->documents->get_document($id);

        foreach ($document->params as $param_name => $param_value) {

            $this->design->assign($param_name, $param_value);
        }

        $loan_id = $document->params->loan_type;
        $loan = $this->Loantypes->get_loantype($loan_id);
        $this->design->assign('loan', $loan);

        $start_date = new DateTime(date('Y-m-d', strtotime($document->params->date)));
        $first_pay = new DateTime(date('Y-m-10', strtotime($document->params->date . '+1 month')));
        $end_date = new DateTime(date('Y-m-10', strtotime($document->params->probably_return_date)));

        $first_pay = $this->check_date($first_pay);

        $payment_schedule = array();

        if (date_diff($start_date, $first_pay)->days < 20) {
            $first_pay_percents = clone $first_pay;

            $period_pay_percents = date_diff($first_pay_percents, $start_date)->days;

            $sum_first_pay = ($document->params->percent / 100) * $document->params->amount * $period_pay_percents;

            $first_pay = new DateTime(date('Y-m-10', strtotime($document->params->date . '+1 month')));
            $first_pay->add(new DateInterval('P1M'));
            $first_pay = $this->check_date($first_pay);

            $payment_schedule[0] = ['date' => $first_pay_percents->format('d.m.Y'), 'sum' => $sum_first_pay];
        }

        if ($first_pay->format('m') == $end_date->format('m')) {
            $percent_per_month = (($document->params->percent / 100) * 365) / 12;
            $annoouitet_pay = $document->params->amount * ($percent_per_month / (1 - pow((1 + $percent_per_month), -$loan->max_period)));
            $annoouitet_pay -= $payment_schedule[0]['sum'];
            $payment_schedule[1] = ['date' => $first_pay->format('d.m.Y'), 'sum' => $annoouitet_pay];
        } else {
            $interval = new DateInterval('P1M');
            $daterange = new DatePeriod($first_pay, $interval, $end_date);

            foreach ($daterange as $date) {
                var_dump($date);
                $this->check_date($date);

                $percent_per_month = (($document->params->percent / 100) * 365) / 12;
                $annoouitet_pay = $document->params->percent->amount * ($percent_per_month / (1 - pow((1 + $percent_per_month), -$loan->max_period)));

                $payment_schedule[$date->format('d.m.Y')] = $annoouitet_pay;
            }
        }

        $this->design->assign('payment_schedule', $payment_schedule);


        $percents_per_year = $document->params->percent * 365;
        $percents = $percents_per_year;
        $percents = number_format($percents, 1, ',', ' ');
        $this->design->assign('percents', $percents);
        $percents_str = explode(',', $percents);

        if (count($percents_str) > 1) {
            $second_part_percents = $percents_str[1];
            $this->design->assign('second_part_percents', $second_part_percents);
        }

        $percents_per_year = $this->num2str($percents_per_year);
        $this->design->assign('percents_per_year', $percents_per_year);
        $amount_to_string = $document->params->amount;
        $amount_to_string = $this->num2str($amount_to_string);


        $this->design->assign('amount_to_string', $amount_to_string);

        $passport_serial_full = explode(' ', $document->params->passport_serial);
        $passport_serial = $passport_serial_full[0];
        $passport_number = $passport_serial_full[1];

        $this->design->assign('passport_serial', $passport_serial);
        $this->design->assign('passport_number', $passport_number);

        $created_date = new DateTime(date('Y-m-d', strtotime($document->params->date)));
        $probably_return_date = new DateTime(date('Y-m-d', strtotime($document->params->probably_return_date)));

        $period_days = date_diff($created_date, $probably_return_date)->days;

        $this->design->assign('$period_days', $period_days);

        $tpl = $this->design->fetch('pdf/' . $document->template);

        if ($this->request->get('action') == 'download_file') {
            $download = true;
            $this->pdf->create($tpl, $document->name, $document->template, $download);
        } else {
            $this->pdf->create($tpl, $document->name, $document->template);
        }

    }

    private function check_date($date)
    {

        for ($i = 0; $i <= 15; $i++) {

            $check_date = $this->WeekendCalendar->check_date($date->format('Y-m-d'));

            if ($check_date == null) {
                break;
            } else {
                $date->sub(new DateInterval('P1D'));
            }
        }

        return $date;
    }

    private function num2str($num)
    {
        $nul = 'ноль';
        $ten = array(
            array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
            array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
        );
        $a20 = array('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
        $tens = array(2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
        $hundred = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
        $unit = array( // Units
            array('копейка', 'копейки', 'копеек', 1),
            array('рубль', 'рубля', 'рублей', 0),
            array('тысяча', 'тысячи', 'тысяч', 1),
            array('миллион', 'миллиона', 'миллионов', 0),
            array('миллиард', 'милиарда', 'миллиардов', 0),
        );
        //
        list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub) > 0) {
            foreach (str_split($rub, 3) as $uk => $v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit) - $uk - 1; // unit key
                $gender = $unit[$uk][3];
                list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2 > 1) $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3]; # 20-99
                else $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk > 1) $out[] = $this->morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
            } //foreach
        } else $out[] = $nul;

        return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
    }

    /**
     * Склоняем словоформу
     * @ author runcore
     */
    private function morph($n, $f1, $f2, $f5)
    {
        $n = abs(intval($n)) % 100;
        if ($n > 10 && $n < 20) return $f5;
        $n = $n % 10;
        if ($n > 1 && $n < 5) return $f2;
        if ($n == 1) return $f1;
        return $f5;
    }
}