<?php

error_reporting(-1);
ini_set('display_errors', 'On');
date_default_timezone_set('Europe/Moscow');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GraphicConstructorController extends Controller
{
    public function fetch()
    {
        if ($this->request->method('post')) {
            if ($this->request->post('action', 'string')) {
                $methodName = 'action_' . $this->request->post('action', 'string');
                if (method_exists($this, $methodName)) {
                    $this->$methodName();
                }
            }
        }

        if ($this->request->get('action') == 'get_companies') {
            $group_id = $this->request->get('group_id');
            $permission = $this->request->get('permission');

            switch ($permission) {
                case 'all':
                    $flag = 3;
                    $search =
                        [
                            'group_id' => $group_id,
                            'permissions' => ['all', 'online', 'offline']
                        ];
                    break;

                case 'online':
                    $flag = 1;
                    $search =
                        [
                            'group_id' => $group_id,
                            'permissions' => ['all', 'online'],
                            'blocked' => 0
                        ];
                    break;

                case 'offline':
                    $flag = 2;
                    $search =
                        [
                            'group_id' => $group_id,
                            'permissions' => ['all', 'offline'],
                            'blocked' => 0
                        ];
                    break;
            }

            $companies = $this->Companies->get_companies($search);
            $loantypes = $this->GroupLoanTypes->get_loantypes_on($group_id, $flag);

            echo json_encode(['companies' => $companies, 'loantypes' => $loantypes]);
            exit;
        }

        if ($this->request->get('start_date')) {
            $this->check_date();
        }

        if ($this->request->get('action') === 'get_branches') {
            $company_id = $this->request->get('company_id');

            $branches = $this->Branches->get_branches(['company_id' => $company_id]);

            echo json_encode($branches);
            exit;
        }

        $loantypes = array();
        foreach ($this->loantypes->get_loantypes() as $lt) {
            $loantypes[$lt->id] = $lt;
        }
        $this->design->assign('loantypes', $loantypes);

        $settlements = $this->OrganisationSettlements->get_settlements();
        $this->design->assign('settlements', $settlements);

        return $this->design->fetch('graphic_constructor.tpl');
    }

    private function check_date()
    {
        $start_date = $this->request->get('start_date');
        $loan_id = $this->request->get('loan_id');
        $branche_id = $this->request->get('branche_id');
        $company_id = $this->request->get('company_id');

        if (empty($branche_id) || in_array($branche_id, ['none', null, 0])) {
            $branches = $this->Branches->get_branches(['company_id' => $company_id]);

            foreach ($branches as $branch) {
                if ($branch->number == '00')
                    $first_pay_day = $branch->payday;
            }
        } else {
            $branch = $this->Branches->get_branch($branche_id);
            $first_pay_day = $branch->payday;
        }

        $loan = $this->Loantypes->get_loantype($loan_id);

        $start_date = date('Y-m-d', strtotime($start_date));
        $first_pay = new DateTime(date('Y-m-' . $first_pay_day, strtotime($start_date)));
        $end_date = date('Y-m-' . $first_pay_day, strtotime($start_date . '+' . $loan->max_period . 'month'));

        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);

        if ($start_date > $first_pay) {
            $first_pay->add(new DateInterval('P1M'));
        }

        $first_pay = $this->check_pay_date($first_pay);

        if (date_diff($first_pay, $start_date)->days <= $loan->min_period && $first_pay->format('m') != $start_date->format('m')) {
            $end_date->add(new DateInterval('P1M'));
        }


        for ($i = 0; $i <= 15; $i++) {
            $check_date = $this->WeekendCalendar->check_date($end_date->format('Y-m-d'));

            if ($check_date == null) {
                break;
            } else {
                $end_date->sub(new DateInterval('P1D'));
            }
        }

        echo json_encode(['date' => $end_date->format('d.m.Y')]);
        exit;
    }

    private function check_pay_date($date)
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

    private function check_pay_date_array($date)
    {
        $weekend = 0;
        for ($i = 0; $i <= 15; $i++) {
            $check_date = $this->WeekendCalendar->check_date($date->format('Y-m-d'));

            if ($check_date == null) {
                break;
            } else {
                $date->sub(new DateInterval('P1D'));
                $weekend++;
            }
        }

        return [$date, $weekend];
    }

    private function action_sum_to_pay()
    {
        $loan_id = $this->request->post('loan_id');
        $amount = $this->request->post('amount');
        $amount = str_replace(' ', '', $amount);
        $date_from = date('Y-m-d', strtotime($this->request->post('date_from')));
        $branch_id = $this->request->post('branch_id');
        $company_id = $this->request->post('company_id');
        $profunion = $this->request->post('profunion');

        $loan = $this->Loantypes->get_loantype($loan_id);

        $url = "https://api-laravel.re-aktiv.ru/api/info/calculator?";
        $params = [
            'tariff_id' => $loan_id,
            'amount' => $amount,
            'start_date' => $date_from,
            'company_id' => $company_id,
            'profunion' => $profunion,
        ];
        $url = $url .= http_build_query($params);
        $result = json_decode(file_get_contents($url), true);

        $payment_schedule = $result['payment_schedule'];
        $first_pay_day = $result['first_pay_day'];
        $annoouitet_pay = $result['annoouitet_pay'];


        $dates[0] = date('d.m.Y', strtotime($date_from));
        $payments[0] = -$amount;

        foreach ($payment_schedule as $date => $pay) {
            if ($date != 'result') {
                $payments[] = round($pay['pay_sum'], '2');
                $dates[] = date('d.m.Y', strtotime($date));
            }
        }

        foreach ($dates as $date) {
            $date = new DateTime(date('Y-m-d H:i:s', strtotime($date)));

            $new_dates[] = mktime(
                $date->format('H'),
                $date->format('i'),
                $date->format('s'),
                $date->format('m'),
                $date->format('d'),
                $date->format('Y')
            );
        }

        $xirr = round($this->Financial->XIRR($payments, $new_dates) * 100, 3);
        $xirr /= 100;

        $psk = round(((pow((1 + $xirr), (1 / 12)) - 1) * 12) * 100, 3);

        $payment_schedule_html = '<table border="2" style="font-size: 15px">
                                        <thead align="center">
                                        <tr style="width: 100%;">
                                            <th rowspan="3">Дата</th>
                                            <th rowspan="3">Сумма</th>
                                            <th colspan="3">Структура платежа</th>
                                            <th rowspan="3">Остаток долга, руб.
                                            </th>
                                        </tr>
                                        <tr style="width: 100%;">
                                            <th>Осн. долг</th>
                                            <th>Проценты</th>
                                            <th>Др. платежи</th>
                                        </tr>
                                        </thead>
                                        <tbody>';

        foreach ($payment_schedule as $date => $payment) {
            if ($date != 'result') {

                $paysum = number_format($payment['pay_sum'], 2, ',', ' ');
                $body_sum = number_format($payment['loan_body_pay'], 2, ',', ' ');
                $percent_sum = number_format($payment['loan_percents_pay'], 2, ',', ' ');
                $comission_sum = number_format((float)$payment['comission_pay'], 2, ',', ' ');
                $rest_sum = number_format($payment['rest_pay'], 2, ',', ' ');

                $payment_schedule_html .= "<tr>";
                $payment_schedule_html .= "<td><input type='text' class='form-control daterange' name='date[][date]' value=" . $date . " disabled></td>";
                $payment_schedule_html .= "<td><input type='text' class='form-control restructure_pay' name='pay_sum[][pay_sum]' value='$paysum' disabled></td>";
                $payment_schedule_html .= "<td><input type='text' class='form-control restructure_od' name='loan_body_pay[][loan_body_pay]' value='$body_sum' disabled></td>";
                $payment_schedule_html .= "<td><input type='text' class='form-control restructure_prc' name='loan_percents_pay[][loan_percents_pay]' value='$percent_sum' disabled></td>";
                $payment_schedule_html .= "<td><input type='text' class='form-control restructure_cms' name='comission_pay[][comission_pay]' value='$comission_sum' disabled></td>";
                $payment_schedule_html .= "<td><input type='text' class='form-control rest_sum' name='rest_pay[][rest_pay]' value='$rest_sum' disabled></td>";
                $payment_schedule_html .= "</tr>";
            }
        }

        $paysum = number_format($payment_schedule['result']['all_sum_pay'], 2, ',', ' ');
        $body_sum = number_format($payment_schedule['result']['all_loan_body_pay'], 2, ',', ' ');
        $percent_sum = number_format($payment_schedule['result']['all_loan_percents_pay'], 2, ',', ' ');
        $comission_sum = number_format((float)$payment_schedule['result']['all_comission_pay'], 2, ',', ' ');
        $rest_sum = number_format($payment_schedule['result']['all_rest_pay_sum'], 2, ',', ' ');
        $psk = number_format($psk, 3, ',', ' ');

        $payment_schedule_html .= "<tr>";
        $payment_schedule_html .= "<td><input type='text' class='form-control daterange' value='ИТОГО' disabled></td>";
        $payment_schedule_html .= "<td><input type='text' class='form-control' value='$paysum' disabled></td>";
        $payment_schedule_html .= "<td><input type='text' class='form-control' value='$body_sum' disabled></td>";
        $payment_schedule_html .= "<td><input type='text' class='form-control' value='$percent_sum' disabled></td>";
        $payment_schedule_html .= "<td><input type='text' class='form-control' value='$comission_sum' disabled></td>";
        $payment_schedule_html .= "<td><input type='text' class='form-control' value='$rest_sum' disabled></td>";
        $payment_schedule_html .= "</tr>";

        $payment_schedule_html .= '</tbody>
                                    </table>';
        $payment_schedule_html .= '<div><br></div>';

        $payment_schedule_html .= '<table border="2" style="font-size: 15px">';
        $payment_schedule_html .= '<tbody>';

        $payment_schedule_html .= "<tr>";
        $payment_schedule_html .= "<td style='width: 70%'><input type='text' class='form-control' value='Минимальный срок до первой выплаты по %%' disabled></td>";
        $payment_schedule_html .= "<td><input type='text' class='form-control' value='$loan->free_period' disabled></td>";
        $payment_schedule_html .= "</td>";

        $payment_schedule_html .= "<tr>";
        $payment_schedule_html .= "<td><input type='text' class='form-control' value='Минимальный срок до первой выплаты по осн.долгу' disabled></td>";
        $payment_schedule_html .= "<td><input type='text' class='form-control' value='$loan->min_period' disabled></td>";
        $payment_schedule_html .= "</td>";

        $payment_schedule_html .= "<tr>";
        $payment_schedule_html .= "<td><input type='text' class='form-control' value='День выплаты з/п' disabled></td>";
        $payment_schedule_html .= "<td><input type='text' class='form-control' value='$first_pay_day' disabled></td>";
        $payment_schedule_html .= "</td>";

        $payment_schedule_html .= '</tbody>';
        $payment_schedule_html .= '</table>';

        /*
        $payment_schedule_html .= "<tr>";
        $payment_schedule_html .= "<td><input type='text' class='form-control daterange' value='ПСК' disabled></td>";
        $payment_schedule_html .= "<td><input type='text' class='form-control' value='$psk' disabled></td>";
        $payment_schedule_html .= "</td>";

        $payment_schedule_html .= "<tr>";
        $payment_schedule_html .= "<td><input type='text' class='form-control daterange' value='Дата выдачи' disabled></td>";
        $payment_schedule_html .= "<td><input type='text' class='form-control' value=$date_from disabled></td>";
        $payment_schedule_html .= "</td>";
        */

        $payment_schedule_html .= '<div><br></div>';

        $payment_schedule_html .= '<div class="link-button"><div class="btn btn-outline-primary download">Получить ссылку на скачивание графика</div></div>';

        echo json_encode(['schedule' => $payment_schedule_html, 'annouitet' => $annoouitet_pay]);
        exit;
    }

    private function action_download_excell()
    {
        $loan_id = $this->request->post('loan_id');
        $amount = $this->request->post('amount');
        $amount = str_replace(' ', '', $amount);
        $date_from = date('Y-m-d', strtotime($this->request->post('date_from')));
        $branch_id = $this->request->post('branch_id');
        $company_id = $this->request->post('company_id');
        $percent = $this->request->post('percents');

        $loan = $this->Loantypes->get_loantype($loan_id);

        if (empty($branch_id) || $branch_id == 'none') {
            $branches = $this->Branches->get_branches(['company_id' => $company_id]);

            foreach ($branches as $branch) {
                if ($branch->number == '00') {
                    $first_pay_day = $branch->payday;
                }
            }
        } else {
            $branch = $this->Branches->get_branch($branch_id);
            $first_pay_day = $branch->payday;
        }

        $rest_sum = $amount;
        $start_date = new DateTime(date('Y-m-d', strtotime($date_from)));
        $paydate = new DateTime(date('Y-m-' . "$first_pay_day", strtotime($start_date->format('Y-m-d'))));
        $paydate->setDate($paydate->format('Y'), $paydate->format('m'), $first_pay_day);

        if ($start_date >= $paydate || date_diff($paydate, $start_date)->days <= $loan->free_period)
            $paydate->add(new DateInterval('P1M'));

        $percent_per_month = (($percent / 100) * 365) / 12;
        $annoouitet_pay = $amount * ($percent_per_month / (1 - pow((1 + $percent_per_month), -$loan->max_period)));
        $annoouitet_pay = round($annoouitet_pay, '2');

        if ($loan_id == 1) {
            $percent_per_month = (($percent / 100) * 360) / 12;
            $annoouitet_pay = $amount * ($percent_per_month / (1 - pow((1 + $percent_per_month), -1)));
            $annoouitet_pay = round($annoouitet_pay, '2');
        }

        $iteration = 0;

        $count_days_this_month = date('t', strtotime($start_date->format('Y-m-d')));

        $paydate = $this->check_pay_date(new DateTime($paydate->format('Y-m-' . $first_pay_day)));

        if (date_diff($paydate, $start_date)->days <= $loan->free_period) {
            $plus_loan_percents = round(($percent / 100) * $amount * date_diff($paydate, $start_date)->days, 2);
            $sum_pay = $annoouitet_pay + $plus_loan_percents;
            $loan_percents_pay = round(($rest_sum * $percent_per_month) + $plus_loan_percents, 2, PHP_ROUND_HALF_DOWN);
            $body_pay = $sum_pay - $loan_percents_pay;
            $paydate->add(new DateInterval('P1M'));
            $iteration++;
        } elseif (date_diff($paydate, $start_date)->days >= $loan->min_period && date_diff($paydate, $start_date)->days < $count_days_this_month) {
            $minus_percents = ($percent / 100) * $amount * ($count_days_this_month - date_diff($paydate, $start_date)->days);
            $sum_pay = $annoouitet_pay - round($minus_percents, 2);
            $loan_percents_pay = ($rest_sum * $percent_per_month) - $minus_percents;
            $loan_percents_pay = round($loan_percents_pay, 2, PHP_ROUND_HALF_DOWN);
            $body_pay = $sum_pay - $loan_percents_pay;
            $iteration++;
        } elseif (date_diff($paydate, $start_date)->days >= $count_days_this_month) {
            $sum_pay = $annoouitet_pay;
            $loan_percents_pay = round($rest_sum * $percent_per_month, 2, PHP_ROUND_HALF_DOWN);
            $body_pay = round($sum_pay - $loan_percents_pay, 2);
            $iteration++;
        } else {
            $sum_pay = ($percent / 100) * $amount * date_diff($paydate, $start_date)->days;
            $loan_percents_pay = $sum_pay;
            $body_pay = 0.00;
        }

        $payment_schedule[$paydate->format('d.m.Y')] =
            [
                'pay_sum' => $sum_pay,
                'loan_percents_pay' => $loan_percents_pay,
                'loan_body_pay' => $body_pay,
                'comission_pay' => 0.00,
                'rest_pay' => $rest_sum -= $body_pay
            ];
        $paydate->add(new DateInterval('P1M'));


        $period = $loan->max_period;
        $period -= $iteration;

        if ($rest_sum != 0) {

            for ($i = 1; $i <= $period; $i++) {
                $paydate->setDate($paydate->format('Y'), $paydate->format('m'), $first_pay_day);
                $date = $this->check_pay_date($paydate);

                if ($i == $period && $loan->id != 1) {
                    $loan_body_pay = $rest_sum;
                    $loan_percents_pay = $annoouitet_pay - $loan_body_pay;
                    $rest_sum = 0.00;
                } elseif ($loan->id == 1) {
                    $loan_body_pay = $rest_sum;
                    $loan_percents_pay = $amount * ($percent / 100) * date_diff($start_date, $date)->days - $loan_percents_pay;
                    $annoouitet_pay = $loan_body_pay + $loan_percents_pay;
                    $rest_sum = 0.00;
                } else {
                    $loan_percents_pay = round($rest_sum * $percent_per_month, 2, PHP_ROUND_HALF_DOWN);
                    $loan_body_pay = round($annoouitet_pay - $loan_percents_pay, 2);
                    $rest_sum = round($rest_sum - $loan_body_pay, 2);
                }

                if (isset($payment_schedule[$date->format('d.m.Y')])) {

                    $date = $this->add_month($date->format('d.m.Y'), 2);
                    $paydate->setDate($date->format('Y'), $date->format('m'), $first_pay_day);
                    $date = $this->check_pay_date($paydate);
                }

                $payment_schedule[$date->format('d.m.Y')] =
                    [
                        'pay_sum' => $annoouitet_pay,
                        'loan_percents_pay' => $loan_percents_pay,
                        'loan_body_pay' => $loan_body_pay,
                        'comission_pay' => 0.00,
                        'rest_pay' => $rest_sum
                    ];

                $paydate->add(new DateInterval('P1M'));
            }
        }

        $payment_schedule['result'] =
            [
                'pay_sum' => 0.00,
                'loan_percents_pay' => 0.00,
                'loan_body_pay' => 0.00,
                'comission_pay' => 0.00,
                'rest_pay' => 0.00
            ];

        foreach ($payment_schedule as $date => $pay) {
            if ($date != 'result') {
                $payment_schedule['result']['pay_sum'] += round($pay['pay_sum'], '2');
                $payment_schedule['result']['loan_percents_pay'] += round($pay['loan_percents_pay'], '2');
                $payment_schedule['result']['loan_body_pay'] += round($pay['loan_body_pay'], 2);
                $payment_schedule['result']['comission_pay'] += round($pay['comission_pay'], '2');
                $payment_schedule['result']['rest_pay'] = 0.00;
            }
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman')->setSize(10);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultRowDimension()->setRowHeight(20);
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(40);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(40);
        $sheet->getColumnDimension('F')->setWidth(40);
        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:B2');
        $sheet->mergeCells('C1:E1');
        $sheet->mergeCells('F1:F2');

        $sheet->setCellValue('A1', 'Дата');
        $sheet->setCellValue('B1', 'Сумма');
        $sheet->setCellValue('C1', 'Структура платежа');

        $sheet->setCellValue('C2', 'Осн. долг');
        $sheet->setCellValue('D2', 'Проценты');
        $sheet->setCellValue('E2', 'Др. платежи');

        $sheet->setCellValue('F1', 'Остаток долга, руб.');

        $i = 3;

        foreach ($payment_schedule as $date => $pay) {
            $sheet->setCellValue('A' . $i, ($date != 'result') ? date('d.m.Y', strtotime($date)) : 'ИТОГО');
            $sheet->setCellValue('B' . $i, number_format($pay['pay_sum']));
            $sheet->setCellValue('C' . $i, number_format($pay['loan_body_pay']));
            $sheet->setCellValue('D' . $i, number_format($pay['loan_percents_pay']));;
            $sheet->setCellValue('E' . $i, number_format($pay['comission_pay']));
            $sheet->setCellValue('F' . $i, number_format($pay['pay_sum']));

            $i++;
        }

        $writer = new Xlsx($spreadsheet);

        $writer->save($this->config->root_dir . "files/constructor.xlsx");

        $link = $this->config->back_url . "/files/constructor.xlsx";

        $html = '<a target="_blank" href='.$link.' download>';
        $html .= '<input type="button"';
        $html .= 'class="btn btn-outline-info"';
        $html .= 'value = "Скачать файл" ></a>';

        echo $html;
        exit;
    }

    private function add_month($date_str, $months)
    {
        $date = new DateTime($date_str);

        // We extract the day of the month as $start_day
        $start_day = $date->format('j');

        // We add 1 month to the given date
        $date->modify("+{$months} month");

        // We extract the day of the month again so we can compare
        $end_day = $date->format('j');

        if ($start_day != $end_day) {
            // The day of the month isn't the same anymore, so we correct the date
            $date->modify('last day of last month');
        }

        return $date;
    }

    private function action_get_groups()
    {
        $permission = $this->request->post('permission');

        switch ($permission) {
            case 'online':
                $blocked = ['all', 'online'];
                break;

            case 'offline':
                $blocked = ['all', 'offline'];
                break;

            default:
                $blocked = ['all', 'offline', 'online', 'nowhere'];
                break;
        }

        $groups = $this->Groups->get_groups(['blocked' => $blocked]);

        if (!empty($groups)) {
            $html = "<option value='none'>Выберите группу</option>";

            foreach ($groups as $group) {
                if ($group->blocked == 'nowhere')
                    $blockCard = "class='badge-danger'";
                else
                    $blockCard = '';

                $html .= "<option $blockCard value='$group->id'>$group->name</option>";
            }

            echo $html;
            exit;
        } else {
            echo json_encode(['empty' => 1]);
            exit;
        }
    }
}
