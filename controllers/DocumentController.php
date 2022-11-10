<?php

error_reporting(-1);
ini_set('display_errors', 'Off');

class DocumentController extends Controller
{
    public function fetch()
    {

        $id = $this->request->get('id');

        $document = $this->documents->get_document($id);

        foreach ($document->params as $param_name => $param_value) {
            $this->design->assign($param_name, $param_value);
        }

        $order = $this->orders->get_order($document->params->order_id);
        $this->design->assign('created_date', $order->date);

        $phone = preg_replace('/(\d)(\d\d\d)(\d\d\d)(\d\d)(\d\d)/', '+$1 ($2) $3-$4-$5', $order->phone_mobile);
        $this->design->assign('phone_mobile', $phone);

        $this->design->assign('doc_type', $document->type);
        $this->design->assign('doc_created', $document->created);

        $settlement = $this->OrganisationSettlements->get_settlement($document->params->settlement_id);
        $order = $this->orders->get_order($document->params->order_id);
        $contracts = $this->contracts->get_contracts(['user_id' => $document->params->user_id, 'status' => [2,3,4]]);
        //заглушка для документов с неполными данными
        $isPlug = false;
        try {
            $group = $this->groups->get_group($order->group_id);
        } catch (\Throwable $th) {
            $group = '00'; //заглушка, нигде не отображается в реальных документах
            $isPlug = true;
        }

        try {
            $company = $this->companies->get_company($order->company_id);
        } catch (\Throwable $th) {
            $company = '00'; //заглушка, нигде не отображается в реальных документах
            $isPlug = true;
        }

        if (!empty($contracts)) {
            $count_contracts = count($contracts);
            $count_contracts = str_pad($count_contracts, 2, '0', STR_PAD_LEFT);
        } else {
            $count_contracts = '01';
        }

        if (isset($order->loan_type)) {
            $loantype = $this->Loantypes->get_loantype($order->loan_type);

            if ($isPlug) {
                $uid = "0";
            } else {
                $uid = "$group->number$company->number $loantype->number $order->personal_number $count_contracts";
            }

            $this->design->assign('uid', $uid);
        }


        $this->design->assign('settlement', $settlement);

        if (isset($document->params->regaddress_id)) {
            $regadress = $this->Addresses->get_address($document->params->regaddress_id);
            $this->design->assign('regadress', $regadress);
        }

        if (isset($document->params->faktaddress_id)) {
            $faktadress = $this->Addresses->get_address($document->params->faktaddress_id);
            $this->design->assign('faktadress', $faktadress);
        }

        $requisite = $this->Requisites->getDefault($document->params->user_id);
        $this->design->assign('requisite', $requisite);

        if (is_null($document->params->company_id)) {
            $company = "0";
        } else {
            $company = $this->Companies->get_company($document->params->company_id);
        }


        $this->design->assign('company', $company);

        if (!empty($document->asp_id)) {
            $code_asp = $this->AspCodes->get_code(['id' => $document->asp_id]);
            $this->design->assign('code_asp', $code_asp);

            $rucred_asp = $this->AspCodes->get_code(['type' => 'rucred_sms', 'order_id' => $document->params->order_id]);
            $this->design->assign('rucred_asp', $rucred_asp);
        }

        $loan_id = $document->params->loan_type;
        $loan = $this->Loantypes->get_loantype($loan_id);
        $this->design->assign('loan', $loan);

        $start_date = new DateTime(date('Y-m-d', strtotime($order->probably_start_date)));
        $end_date = new DateTime(date('Y-m-d', strtotime($order->probably_return_date)));

        $period = date_diff($start_date, $end_date)->days;

        $this->design->assign('period', $period);

        if (isset($document->params->payment_schedule['schedule'])) {
            $payment_schedule = json_decode($document->params->payment_schedule['schedule'], true);
        } else {
            //Заглушка от ошибок для первого документа (в нем отсутствуют эти данные)
            $payment_schedule = [
                "09.12.2022" => [
                    "pay_sum" => 0,
                    "loan_percents_pay" => 0,
                    "loan_body_pay" => 0,
                    "comission_pay" => 0,
                    "rest_pay" => 0
                ],
                "result" => [
                    "all_sum_pay" => 0.1,
                    "all_loan_percents_pay" => 0.1,
                    "all_loan_body_pay" => 0,
                    "all_comission_pay" => 0,
                    "all_rest_pay_sum" => 0
                ]
            ];
        }


        uksort(
            $payment_schedule,
            function ($a, $b) {

                if ($a == $b) {
                    return 0;
                }

                return (date('Y-m-d', strtotime($a)) < date('Y-m-d', strtotime($b))) ? -1 : 1;
            });

        if ($document->type == 'ZAYAVLENIE_RESTRUCT') {
            $old_schedule = (array)$this->PaymentsSchedules->get(['actual' => 0, 'order_id' => $document->params->order_id]);
            $old_schedule = json_decode($old_schedule['schedule'], true);

            unset($old_schedule['result']);

            $current_schedule = (array)$this->PaymentsSchedules->get(['actual' => 1, 'order_id' => $document->params->order_id]);
            $current_schedule = json_decode($current_schedule['schedule'], true);

            unset($current_schedule['result']);

            $term = (count($current_schedule) > count($old_schedule)) ? count($current_schedule) - count($old_schedule) : 'no';

            if ($term == 'no')
                $term = 0;

            $string_term = $this->num2str($term);
            $this->design->assign('term', $term);
            $this->design->assign('string_term', $string_term);

            foreach ($current_schedule as $schedule) {
                if (isset($schedule['last_pay'])) {
                    $pay_sum = number_format(floatval($schedule['pay_sum']), 2, ',', '');
                    $loan_body_pay = number_format(floatval($schedule['loan_body_pay']), 2, ',', '');
                    $loan_percents_pay = number_format(floatval($schedule['loan_percents_pay']), 2, ',', '');
                    $comission_pay = number_format(floatval($schedule['comission_pay']), 2, ',', '');
                }
            }

            $pay_sum = explode(',', $pay_sum);
            $loan_body_pay = explode(',', $loan_body_pay);
            $loan_percents_pay = explode(',', $loan_percents_pay);
            $comission_pay = explode(',', $comission_pay);

            $pay_sum_string =
                [
                    0 => $this->num2str($pay_sum[0]),
                    1 => $this->num2str($pay_sum[1])
                ];
            $loan_body_pay_string =
                [
                    0 => $this->num2str($loan_body_pay[0]),
                    1 => $this->num2str($loan_body_pay[1])
                ];
            $loan_percents_pay_string =
                [
                    0 => $this->num2str($loan_percents_pay[0]),
                    1 => $this->num2str($loan_percents_pay[1])
                ];
            $comission_pay_string =
                [
                    0 => $this->num2str($comission_pay[0]),
                    1 => $this->num2str($comission_pay[1])
                ];

            $this->design->assign('pay_sum', $pay_sum);
            $this->design->assign('loan_body_pay', $loan_body_pay);
            $this->design->assign('loan_percents_pay', $loan_percents_pay);
            $this->design->assign('comission_pay', $comission_pay);

            $this->design->assign('pay_sum_string', $pay_sum_string);
            $this->design->assign('loan_body_pay_string', $loan_body_pay_string);
            $this->design->assign('loan_percents_pay_string', $loan_percents_pay_string);
            $this->design->assign('comission_pay_string', $comission_pay_string);
        }

        $all_pay_sum_string = explode('.', $payment_schedule['result']['all_sum_pay']);

        $all_pay_sum_string_part_one = $this->num2str($all_pay_sum_string[0]);
        $all_pay_sum_string_part_two = substr($all_pay_sum_string[1], 0, 2);

        $this->design->assign('all_pay_sum_string_part_two', $all_pay_sum_string_part_two);
        $this->design->assign('all_pay_sum_string_part_one', $all_pay_sum_string_part_one);

        $all_percents_string = explode('.', $payment_schedule['result']['all_loan_percents_pay']);
        $all_percents_string_part_one = $this->num2str($all_percents_string[0]);

        $all_percents_string_part_two = str_pad($all_percents_string[1], '2', '0', STR_PAD_RIGHT);
        $this->design->assign('all_percents_string_part_two', $all_percents_string_part_two);
        $this->design->assign('all_percents_string', $all_percents_string);

        if (is_null($document->params->percent)) {
            $percents_per_day_str_part_one = 0;
            $percents_per_day_str_part_two = 0;
        } else {
            $percents_per_day_str = explode('.', $document->params->percent);
            $percents_per_day_str_part_one = $this->num2str($percents_per_day_str[0]);
            $percents_per_day_str_part_two = $this->num2str($percents_per_day_str[1]);
        }


        $this->design->assign('percents_per_day_str_part_one', $percents_per_day_str_part_one);
        $this->design->assign('percents_per_day_str_part_two', $percents_per_day_str_part_two);

        $period_str = $this->num2str($period);

        $this->design->assign('period_str', $period_str);

        $this->design->assign('all_percents_string_part_one', $all_percents_string_part_one);

        $first_part_all_sum_pay = explode('.', $payment_schedule['result']['all_sum_pay']);
        $first_part_all_sum_pay = $first_part_all_sum_pay[0];

        $this->design->assign('payment_schedule', $payment_schedule);
        $this->design->assign('first_part_all_sum_pay', $first_part_all_sum_pay);


        if (isset($document->params->payment_schedule['psk'])) {
            $percents_per_year = $document->params->payment_schedule['psk'];
        } else {
            $percents_per_year = 1; //заглушка
        }
        $percents = $percents_per_year;

        $percents = number_format($percents, 3, ',', ' ');
        $percents = str_pad($percents, 6, '0', STR_PAD_RIGHT);

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
        $contract = $this->contracts->get_contract($order->contract_id);

        if (!empty($contract->number)) {
            $order->uid = $contract->number;
        } else {
            $order->uid = "$order->uid ({$order->order_id})";
        }

        $fio = $document->params->lastname . ' ' . mb_substr($document->params->firstname, 0, 1) . mb_substr($document->params->patronymic, 0, 1);
        $uid = $document->params->uid;
        $employer = explode(' ', $uid);
        $employer = "$employer[0]$employer[1]";
        $date = date('Y-m-d', strtotime($document->params->probably_start_date));

        if ($document->template == 'individualnie_usloviya.tpl') {
            $file_name = "$order->uid Form 040302 $date";
            $file_name = $this->translit($file_name);
        }

        if ($document->template == 'ind_usloviya_online.tpl') {
            $file_name = "$order->uid Form 040301 $date";
            $file_name = $this->translit($file_name);
        }

        if ($document->template == 'soglasie_na_obr_pers_dannih.tpl') {
            $file_name = "$order->personal_number Form 0405 $date";
            $file_name = $this->translit($file_name);
        }

        if ($document->template == 'soglasie_rdb.tpl') {
            $file_name = "$order->personal_number Form 04052 $date";
            $file_name = $this->translit($file_name);
        }

        if ($document->template == 'soglasie_minb.tpl') {
            $file_name = "$order->personal_number Form 04051 $date";
            $file_name = $this->translit($file_name);
        }

        if ($document->template == 'soglasie_rabotadatelu.tpl') {
            $file_name = "$order->personal_number Form 0406 ($employer) $date";
            $file_name = $this->translit($file_name);
        }

        if ($document->template == 'soglasie_rukred_rabotadatel.tpl') {
            $file_name = "$order->personal_number Form 0303 ($employer) $date";
            $file_name = $this->translit($file_name);
        }

        if ($document->template == 'soglasie_na_kred_otchet.tpl') {
            $file_name = "$order->personal_number Form 0407 $date";
            $file_name = $this->translit($file_name);
        }

        if ($document->template == 'zayavlenie_na_perechislenie_chasti_zp.tpl') {
            $file_name = "$order->uid Form 0409 $date";
            $file_name = $this->translit($file_name);
        }

        if ($document->template == 'zayavlenie_zp_v_schet_pogasheniya_mrk.tpl') {
            $file_name = "$order->uid Form 0304 $date";
            $file_name = $this->translit($file_name);
        }

        if ($document->template == 'grafik_obsl_mkr.tpl') {
            $file_name = "$order->uid Form 0404 $date";
            $file_name = $this->translit($file_name);
        }

        if ($document->template == 'obshie_uslovia.tpl') {
            $file_name = "$order->uid Form 0410 $date";
            $file_name = $this->translit($file_name);
        }

        if ($document->template == 'perechislenie_zaemnih_sredstv.tpl') {
            $file_name = "$order->uid Form 0412 $date";
            $file_name = $this->translit($file_name);
        }

        if ($document->template == 'dop_grafik.tpl') {
            $file_name = $fio . " - График платежей по микрозайму (после реструктуризации)" . "($date)";
        }

        if ($document->template == 'dop_soglashenie.tpl') {
            $file_name = $fio . " - Дополнительное соглашение к Индивидуальным условиям договора микрозайма" . "($date)";
        }

        if (isset($file_name))
            $file_name = $this->translit($file_name);


        if ($this->request->get('action') == 'download_file') {
            $this->pdf->create($tpl, $document->name, $file_name, 1);
        } else {
            $this->pdf->create($tpl, $document->name, $file_name);
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
