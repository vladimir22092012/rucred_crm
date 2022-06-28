<?php

class YaDisk extends Core
{
    protected $token;
    protected $disk;

    public function __construct()
    {
        parent::__construct();
        $this->token = 'AQAAAABcOalaAADLWxIYdswB4kYFjIrgW6xGURU';
        $this->disk = new Arhitector\Yandex\Disk($this->token);
    }

    public function upload_orders_files($order_id, $upload_scans)
    {
        $order = $this->orders->get_order($order_id);

        $fio = $order->lastname . ' ' . mb_substr($order->firstname, 0, 1) . mb_substr($order->patronymic, 0, 1);
        $translit_lastname = $this->translit($order->lastname);
        $employer = explode(' ', $order->uid);
        $employer = "$employer[0]$employer[1]";
        $date = date('Y-m-d', strtotime($order->probably_start_date));
        $bank = ($order->settlement_id == 2) ? 'МИнБанк' : 'РосДорБанк';

        if ($upload_scans == 1) {
            $upload_files = $this->Scans->get_scans_by_order_id($order_id);
        } else {
            $upload_files = $this->Documents->get_documents(['order_id' => $order_id]);
        }

        try {
            $resource = $this->disk->getResource('disk:/RC3100 CRM Data/3102 Loans/' . $order->personal_number . ' ' . $translit_lastname . '/');
            $resource->create();
        } catch (Exception $e) {

        }

        foreach ($upload_files as $document) {

            if ($upload_scans == 1)
                $type = $document->type;
            else
                $type = $document->template;

            if ($type == 'individualnie_usloviya.tpl') {
                $file_name = $fio . ' - Договор микрозайма ' . $order->uid . ' ' . "($date)" . ' ' . $bank;
            }

            if ($type == 'soglasie_na_obr_pers_dannih.tpl') {
                $file_name = $fio . ' - Согласие на обработку ПД ' . $order->uid . ' ' . "($date)";
            }

            if ($type == 'soglasie_rdb.tpl') {
                $file_name = $fio . ' - Согласие на идентификацию через банк РДБ ' . $order->uid . ' ' . "($date)";
            }

            if ($type == 'soglasie_minb.tpl') {
                $file_name = $fio . ' - Согласие на идентификацию через банк МИНБ ' . $order->uid . ' ' . "($date)";
            }

            if ($type == 'soglasie_rabotadatelu.tpl') {
                $file_name = $fio . ' - Согласие на обработку и передачу ПД работодателю ' . $employer . ' ' . "($date)";
            }

            if ($type == 'soglasie_rukred_rabotadatel.tpl') {
                $file_name = $fio . " - Согласие работодателю $employer на обработку и передачу ПД " . "($date)";
            }

            if ($type == 'soglasie_na_kred_otchet.tpl') {
                $file_name = $fio . " - Согласие на запрос КО " . "($date)";
            }

            if ($type == 'zayavlenie_na_perechislenie_chasti_zp.tpl') {
                $file_name = $fio . " - Обязательство подать заявление работодателю $employer  на перечисление" . "($date)";
            }

            if ($type == 'zayavlenie_zp_v_schet_pogasheniya_mrk.tpl') {
                $file_name = $fio . " - Заявление работодателю $employer  на перечисление по микрозайму " . "($date)";
            }

            if ($type == 'grafik_obsl_mkr.tpl') {
                $file_name = $fio . " - График обслуживания микрозайма" . "($date)";
            }

            if ($type == 'perechislenie_zaemnih_sredstv.tpl') {
                $file_name = $fio . " - Согласие на перечисление заемных средств" . "($date)";
            }

            if (isset($file_name)) {
                $file_name = $this->translit($file_name);
                $resource = $this->disk->getResource('disk:/RC3100 CRM Data/3102 Loans/' . $order->personal_number . ' ' . $translit_lastname . '/' . $file_name . '.pdf');

                if ($upload_scans == 1)
                    $resource->upload($this->config->root_url . '/files/users/' . $order->user_id . '/' . $document->name, true);
                else{

                    foreach ($document->params as $param_name => $param_value) {
                        $this->design->assign($param_name, $param_value);
                    }

                    $settlement = $this->OrganisationSettlements->get_settlement($document->params->settlement_id);

                    $this->design->assign('settlement', $settlement);

                    $regadress = $this->Addresses->get_address($document->params->regaddress_id);
                    $this->design->assign('regadress', $regadress);

                    $faktadress = $this->Addresses->get_address($document->params->faktaddress_id);
                    $this->design->assign('faktadress', $faktadress);


                    $company = $this->Companies->get_company($document->params->company_id);
                    $this->design->assign('company', $company);

                    $code_asp = $this->AspCodes->get_code(['code' => $document->params->sms]);
                    $this->design->assign('code_asp', $code_asp);

                    $loan_id = $document->params->loan_type;
                    $loan = $this->Loantypes->get_loantype($loan_id);
                    $this->design->assign('loan', $loan);

                    $start_date = new DateTime(date('Y-m-d', strtotime($document->params->probably_start_date)));
                    $end_date = new DateTime(date('Y-m-10', strtotime($document->params->probably_return_date)));

                    $period = date_diff($start_date, $end_date)->days;

                    $this->design->assign('period', $period);

                    $payment_schedule = json_decode($document->params->payment_schedule, true);
                    $payment_schedule = end($payment_schedule);

                    uksort(
                        $payment_schedule,
                        function ($a, $b) {

                            if ($a == $b) {
                                return 0;
                            }

                            return (date('Y-m-d', strtotime($a)) < date('Y-m-d', strtotime($b))) ? -1 : 1;
                        }
                    );

                    $all_pay_sum_string = explode('.', $payment_schedule['result']['all_sum_pay']);

                    $all_pay_sum_string_part_one = $this->num2str($all_pay_sum_string[0]);
                    $all_pay_sum_string_part_two = substr($all_pay_sum_string[1], 0, 2);

                    $this->design->assign('all_pay_sum_string_part_two', $all_pay_sum_string_part_two);
                    $this->design->assign('all_pay_sum_string_part_one', $all_pay_sum_string_part_one);

                    $all_percents_string = explode('.', $payment_schedule['result']['all_loan_percents_pay']);
                    $all_percents_string_part_one = $this->num2str($all_percents_string[0]);

                    $all_percents_string_part_two = substr($all_percents_string[1], 0, 2);
                    $this->design->assign('all_percents_string_part_two', $all_percents_string_part_two);

                    $percents_per_day_str = explode('.', $document->params->percent);
                    $percents_per_day_str_part_one = $this->num2str($percents_per_day_str[0]);
                    $percents_per_day_str_part_two = $this->num2str($percents_per_day_str[1]);

                    $this->design->assign('percents_per_day_str_part_one', $percents_per_day_str_part_one);
                    $this->design->assign('percents_per_day_str_part_two', $percents_per_day_str_part_two);

                    $period_str = $this->num2str($period);

                    $this->design->assign('period_str', $period_str);

                    $this->design->assign('all_percents_string_part_one', $all_percents_string_part_one);

                    $first_part_all_sum_pay = explode('.', $payment_schedule['result']['all_sum_pay']);
                    $first_part_all_sum_pay = $first_part_all_sum_pay[0];

                    $this->design->assign('payment_schedule', $payment_schedule);
                    $this->design->assign('first_part_all_sum_pay', $first_part_all_sum_pay);


                    $percents_per_year = $document->params->psk;
                    $percents = $percents_per_year;

                    $percents = number_format($percents, 3, ',', ' ');

                    $this->design->assign('percents', $percents);
                    $percents_str = explode(',', $percents);

                    if (count($percents_str) > 1) {
                        $second_part_percents = $percents_str[1];
                        $second_part_percents = $this->num2str($second_part_percents);
                        $this->design->assign('second_part_percents', $second_part_percents);
                    }

                    $percents_per_year = $this->num2str($percents_per_year);
                    $this->design->assign('percents_per_year', $percents_per_year);
                    $psk_rub = $payment_schedule['result']['all_loan_percents_pay'] + $payment_schedule['result']['all_comission_pay'];
                    $psk_rub = number_format($psk_rub, 2, ',', ' ');

                    $amount_to_string = explode(',', $psk_rub);
                    $amount_to_string_1 = str_replace(' ', '', $amount_to_string[0]);

                    $amount_to_string_1 = $this->num2str($amount_to_string_1);
                    $this->design->assign('amount_to_string_1', $amount_to_string_1);

                    if (isset($amount_to_string[1])) {
                        $amount_to_string_2 = $this->num2str($amount_to_string[1]);
                        $this->design->assign('amount_to_string_2', $amount_to_string_2);
                    }

                    $this->design->assign('psk_rub', $psk_rub);

                    $amount_string = $this->num2str($document->params->amount);
                    $this->design->assign('amount_string', $amount_string);

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
                    $this->pdf->create($tpl, $document->name, $document->template, $download = false, $file_name);
                    $resource->upload(ROOT . '/files/users/'. $file_name.'.pdf', true);
                    unlink(ROOT . '/files/users/'. $file_name.'.pdf');
                }
            }
        }
    }

    private function translit($value)
    {
        $converter = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
            'Е' => 'E', 'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
            'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
            'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'Ch',
            'Ш' => 'Sh', 'Щ' => 'Sch', 'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        );

        $value = strtr($value, $converter);
        return $value;
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
                if (!intval($v)) {
                    continue;
                }
                $uk = sizeof($unit) - $uk - 1; // unit key
                $gender = $unit[$uk][3];
                list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2 > 1) {
                    $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3]; # 20-99
                } else {
                    $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                }
                // units without rub & kop
                if ($uk > 1) {
                    $out[] = $this->morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
                }
            } //foreach
        } else {
            $out[] = $nul;
        }

        return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
    }

    private function morph($n, $f1, $f2, $f5)
    {
        $n = abs(intval($n)) % 100;
        if ($n > 10 && $n < 20) {
            return $f5;
        }
        $n = $n % 10;
        if ($n > 1 && $n < 5) {
            return $f2;
        }
        if ($n == 1) {
            return $f1;
        }
        return $f5;
    }
}