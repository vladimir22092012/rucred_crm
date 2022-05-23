<?php

use App\Services\MailService;

error_reporting(-1);
ini_set('display_errors', 'Off');

class NeworderController extends Controller
{
    public function fetch()
    {
        if ($this->request->method('post')) {
            if ($this->request->post('action', 'string')) {
                $methodName = 'action_' . $this->request->post('action', 'string');
                if ( method_exists($this, $methodName) ) {
                    $this->$methodName();
                }
            }
        }

        if ($this->request->get('order_id')) {
            $order_id = $this->request->get('order_id');

            $order = $this->orders->get_order($order_id);

            $fio_spouse = explode(' ', $order->fio_spouse);

            $passport_serial = explode(' ', $order->passport_serial);

            $order->passport_serial = $passport_serial[0];
            $order->passport_number = $passport_serial[1];

            $this->design->assign('order', $order);
            $this->design->assign('fio_spouse', $fio_spouse);
        }

        if ($this->request->get('start_date')) {
            $this->check_date();
        }

        if ($this->request->get('action') == 'get_companies') {
            $group_id = $this->request->get('group_id');

            $companies = $this->Companies->get_companies(['group_id' => $group_id, 'blocked' => 0]);
            $loantypes = $this->GroupLoanTypes->get_loantypes_on($group_id);

            echo json_encode(['companies' => $companies, 'loantypes' => $loantypes]);
            exit;
        }

        if ($this->request->get('action') === 'get_branches') {
            $company_id = $this->request->get('company_id');

            $branches = $this->Branches->get_branches(['company_id' => $company_id]);

            echo json_encode($branches);
            exit;
        }

        if ($this->request->get('action') === 'check_same_users') {

            $user['lastname'] = $this->request->get('lastname');
            $user['firstname'] = $this->request->get('firstname');

            if($this->request->get('patronymic'))
                $user['patronymic'] = $this->request->get('patronymic');

            $user['birth'] = date('d.m.Y', strtotime($this->request->get('birth')));

            $users = $this->users->check_exist_users($user);

            if(!empty($users))
                echo json_encode($users);
            else
                echo json_encode(['empty' => 'Совпадений не найдено']);
            exit;
        }

        $loantypes = array();
        foreach ($this->loantypes->get_loantypes() as $lt) {
            $loantypes[$lt->id] = $lt;
        }
        $this->design->assign('loantypes', $loantypes);

        $groups = $this->Groups->get_groups();
        $this->design->assign('groups', $groups);

        return $this->design->fetch('offline/neworder.tpl');
    }

    private function action_create_new_order()
    {
        $amount = preg_replace("/[^,.0-9]/", '', $this->request->post('amount'));
        $start_date = new DateTime(date('Y-m-d', strtotime($this->request->post('start_date'))));

        if ($this->request->post('end_date')) {
            $end_date = date('Y-m-d', strtotime($this->request->post('end_date')));
            $end_date = new DateTime(date('Y-m-d', strtotime($end_date)));
            $period = date_diff($start_date, $end_date)->days;
        } else {
            $count_mounth = $this->request->post('date_to_select');
            $end_date = clone $start_date;
            $end_date = $end_date->add(new DateInterval('P' . $count_mounth . 'M'));
            $period = date_diff($start_date, $end_date)->days;
        }

        $percent = (float)$this->request->post('percent');
        $charge = (float)$this->request->post('charge');
        $insure = (float)$this->request->post('insure');
        $peni = (float)$this->request->post('peni');

        $this->design->assign('percent', $percent);
        $this->design->assign('charge', $charge);
        $this->design->assign('peni', $peni);
        $this->design->assign('amount', $amount);
        $this->design->assign('period', $period);

        $user = array();

        $user['user_id'] = intval($this->request->post('user_id'));

        $user['firstname'] = trim($this->request->post('firstname'));
        $user['lastname'] = trim($this->request->post('lastname'));
        $user['patronymic'] = trim($this->request->post('patronymic'));

        $user['fio_acc_holder'] = trim($this->request->post('fio_acc_holder'));
        $user['account_number'] = trim($this->request->post('account_number'));
        $user['bank_name'] = trim($this->request->post('bank_name'));
        $user['bik_bank'] = trim($this->request->post('bik_bank'));

        $cards_bank_name = $this->request->post('cards_bank_name');
        $cards_limit = $this->request->post('cards_limit');
        $cards_rest_sum = $this->request->post('cards_rest_sum');
        $cards_validity_period = $this->request->post('cards_validity_period');
        $cards_delay = $this->request->post('cards_delay');

        $credits_bank_name = $this->request->post('credits_bank_name');
        $credits_rest_sum = $this->request->post('credits_rest_sum');
        $credits_month_pay = $this->request->post('credits_month_pay');
        $credits_return_date = $this->request->post('credits_return_date');
        $credits_percents = $this->request->post('credits_percents');
        $credits_delay = $this->request->post('credits_delay');

        $attestation_date = $this->request->post('date');
        $attestation_comment = $this->request->post('comment');

        $attestations = json_encode(array_replace_recursive($attestation_date, $attestation_comment));

        if ($this->request->post('no_attestation') === 1) {
            $attestations = null;
        }

        $user['attestation'] = $attestations;

        $credits_story = json_encode(array_replace_recursive($credits_bank_name, $credits_rest_sum, $credits_month_pay, $credits_return_date, $credits_percents, $credits_delay));
        $cards_story = json_encode(array_replace_recursive($cards_bank_name, $cards_limit, $cards_rest_sum, $cards_validity_period, $cards_delay));

        $user['credits_story'] = $credits_story;
        $user['cards_story'] = $cards_story;

        $profunion = $this->request->post('profunion');

        if ($profunion == 0) {
            if ($this->request->post('want_profunion') == 1) {
                $user['profunion'] = 2;
            } else {
                $user['profunion'] = 0;
            }
        } else {
            $user['profunion'] = 1;
        }

        $user['workplace'] = $this->request->post('company_input');

        $change_fio = $this->request->post('change_fio');

        if ($change_fio == 1) {
            $user['prev_fio'] = $this->request->post('prev_fio');
            $user['fio_change_date'] = $this->request->post('fio_change_date');
        }

        $user['sex'] = (int)$this->request->post('sex');
        $user['fio_spouse'] = $this->request->post('fio_spouse');
        $user['fio_spouse'] = implode(' ', $user['fio_spouse']);

        $user['phone_spouse'] = $this->request->post('phone_spouse');

        $user['foreign_flag'] = (int)$this->request->post('foreign');
        $user['foreign_husb_wife'] = (int)$this->request->post('foreign_husb_wife');
        $user['fio_public_spouse'] = $this->request->post('fio_public_spouse');
        $user['foreign_relative'] = (int)$this->request->post('foreign_relative');
        $user['fio_relative'] = $this->request->post('fio_relative');


        $user['phone_mobile'] = trim((string)$this->request->post('phone'));
        $user['phone_mobile_confirmed'] = (int)$this->request->post('phone_confirmed');

        if ($this->request->post('viber_same') === 1) {
            $user['viber_num'] = $user['phone_mobile'];
        } else {
            $user['viber_num'] = trim($this->request->post('viber'));
        }

        if ($this->request->post('whatsapp_same') === 1) {
            $user['viber_num'] = $user['phone_mobile'];
        } else {
            $user['viber_num'] = trim($this->request->post('whatsapp'));
        }

        if ($this->request->post('telegram_same') === 1) {
            $user['viber_num'] = $user['phone_mobile'];
        } else {
            $user['viber_num'] = trim($this->request->post('telegram'));
        }


        $user['email'] = trim((string)$this->request->post('email'));
        $user['gender'] = trim((string)$this->request->post('gender'));
        $user['birth'] = trim((string)$this->request->post('birth'));
        $user['birth_place'] = trim((string)$this->request->post('birth_place'));

        $user['push_not'] = (int)$this->request->post('push_not');
        $user['sms_not'] = (int)$this->request->post('sms_not');
        $user['email_not'] = (int)$this->request->post('email_not');
        $user['massanger_not'] = (int)$this->request->post('massanger_not');


        $passport_serial = (string)$this->request->post('passport_serial');
        $passport_number = (string)$this->request->post('passport_number');

        $user['passport_serial'] = "$passport_serial $passport_number";

        $user['snils'] = (string)$this->request->post('snils');
        $user['inn'] = (string)$this->request->post('inn');

        $user['passport_date'] = (string)$this->request->post('passport_date');
        $user['passport_issued'] = (string)$this->request->post('passport_issued');
        $user['subdivision_code'] = (string)$this->request->post('subdivision_code');
        $user['income'] = preg_replace("/[^,.0-9]/", '', $this->request->post('income_medium'));
        $user['expenses'] = preg_replace("/[^,.0-9]/", '', $this->request->post('outcome_medium'));

        $Regadress = json_decode($this->request->post('Regadress'));

        $user['Regadressfull'] = $this->request->post('Regadressfull');

        $user['Regindex'] = (!empty($Regadress->data->postal_code)) ? $Regadress->data->postal_code : '';
        $user['Regregion'] = (!empty($Regadress->data->region)) ? $Regadress->data->region : '';
        $user['Regregion_shorttype'] = (!empty($Regadress->data->region_type)) ? $Regadress->data->region_type : '';
        $user['Regcity'] = (!empty($Regadress->data->city)) ? $Regadress->data->city : '';
        $user['Regcity_shorttype'] = (!empty($Regadress->data->city_type)) ? $Regadress->data->city_type : '';
        $user['Regdistrict'] = (!empty($Regadress->data->city_district)) ? $Regadress->data->city_district : '';
        $user['Regdistrict_shorttype'] = (!empty($Regadress->data->city_district_type)) ? $Regadress->data->city_district_type : '';
        $user['Reglocality'] = (!empty($Regadress->data->settlement)) ? $Regadress->data->settlement : '';
        $user['Reglocality_shorttype'] = (!empty($Regadress->data->settlement_type)) ? $Regadress->data->settlement_type : '';
        $user['Regstreet'] = (!empty($Regadress->data->street)) ? $Regadress->data->street : '';
        $user['Regstreet_shorttype'] = (!empty($Regadress->data->street_type)) ? $Regadress->data->street_type : '';
        $user['Reghousing'] = (!empty($Regadress->data->house)) ? $Regadress->data->house : '';
        $user['Regbuilding'] = (!empty($Regadress->data->block)) ? $Regadress->data->block : '';
        $user['Regroom'] = (!empty($Regadress->data->flat)) ? $Regadress->data->flat : '';
        $user['loan_history'] = '[]';

        if ($this->request->post('actual_address') === 1) {
            $user['Faktadressfull'] = $user['Regadressfull'];
            $user['Faktindex'] = $user['Regindex'];
            $user['Faktregion'] = $user['Regregion'];
            $user['Faktregion_shorttype'] = $user['Regregion_shorttype'];
            $user['Faktdistrict'] = $user['Regdistrict'];
            $user['Faktdistrict_shorttype'] = $user['Regdistrict_shorttype'];
            $user['Faktcity'] = $user['Regcity'];
            $user['Faktcity_shorttype'] = $user['Regcity_shorttype'];
            $user['Faktlocality'] = $user['Reglocality'];
            $user['Faktlocality_shorttype'] = $user['Reglocality_shorttype'];
            $user['Faktstreet'] = $user['Regstreet'];
            $user['Faktstreet_shorttype'] = $user['Regstreet_shorttype'];
            $user['Fakthousing'] = $user['Reghousing'];
            $user['Faktbuilding'] = $user['Regbuilding'];
            $user['Faktroom'] = $user['Regroom'];
        } else {
            $Faktaddress = json_decode($this->request->post('Fakt_adress'));

            $user['Faktadressfull'] = $this->request->post('Faktadressfull');

            $user['Faktindex'] = (!empty($Faktaddress->data->postal_code)) ? $Faktaddress->data->postal_code : '';
            $user['Faktregion'] = (!empty($Faktaddress->data->region)) ? $Faktaddress->data->region : '';
            $user['Faktregion_shorttype'] = (!empty($Faktaddress->data->region_type)) ? $Faktaddress->data->region_type : '';
            $user['Faktcity'] = (!empty($Faktaddress->data->city)) ? $Faktaddress->data->city : '';
            $user['Faktcity_shorttype'] = (!empty($Faktaddress->data->city_type)) ? $Faktaddress->data->city_type : '';
            $user['Faktdistrict'] = (!empty($Faktaddress->data->city_district)) ? $Faktaddress->data->city_district : '';
            $user['Faktdistrict_shorttype'] = (!empty($Faktaddress->data->city_district_type)) ? $Faktaddress->data->city_district_type : '';
            $user['Faktlocality'] = (!empty($Faktaddress->data->settlement)) ? $Faktaddress->data->settlement : '';
            $user['Faktlocality_shorttype'] = (!empty($Faktaddress->data->settlement_type)) ? $Faktaddress->data->settlement_type : '';
            $user['Faktstreet'] = (!empty($Faktaddress->data->street)) ? $Faktaddress->data->street : '';
            $user['Faktstreet_shorttype'] = (!empty($Faktaddress->data->street_type)) ? $Faktaddress->data->street_type : '';
            $user['Fakthousing'] = (!empty($Faktaddress->data->house)) ? $Faktaddress->data->house : '';
            $user['Faktbuilding'] = (!empty($Faktaddress->data->block)) ? $Faktaddress->data->block : '';
            $user['Faktroom'] = (!empty($Faktaddress->data->flat)) ? $Faktaddress->data->flat : '';
        }

        $user['company_id'] = (int)$this->request->post('company', 'integer');
        $user['group_id'] = (int)$this->request->post('group', 'integer');
        $user['branche_id'] = (int)$this->request->post('branch', 'integer');


        if (empty($user['user_id'])) {
            $user['stage_personal'] = 1;
            $user['stage_passport'] = 1;
            $user['stage_address'] = 1;
            $user['stage_work'] = 1;
            $user['stage_files'] = 1;
            $user['stage_card'] = 1;
            unset($user['user_id']);

            $last_personal_number = $this->users->last_personal_number();

            $user['personal_number'] = $last_personal_number + 1;

            if ($user['user_id'] = $this->users->add_user($user)) {
            } else {
                $this->design->assign('error', 'Не удалось создать клиента');
            }
        }

        $loan_type = $this->request->post('loan_type_to_submit');

        if (!empty($user['user_id'])) {
            $user_id = $user['user_id'];
            unset($user['user_id']);

            $this->users->update_user($user_id, $user);

            $settlements = $this->OrganisationSettlements->get_settlements();

            foreach ($settlements as $key => $settlement) {
                if ($settlement->std != 1) {
                    $settlement_std = $settlement;
                }
            }

            $order = array(
                'user_id' => $user_id,
                'amount' => $amount,
                'period' => $period,
                'date' => date('Y-m-d H:i:s'),
                'manager_id' => $this->manager->id,
                'status' => ($this->request->post('draft')) ? 12 : 1,
                'offline' => 1,
                'charge' => $charge,
                'insure' => $insure,
                'loan_type' => (int)$loan_type,
                'probably_return_date' => date('Y-m-d H:i:s', strtotime($this->request->post('end_date'))),
                'probably_start_date' => date('Y-m-d H:i:s', strtotime($this->request->post('start_date'))),
                'probably_return_sum' => (int)preg_replace("/[^,.0-9]/", '', $this->request->post('probably_return_sum')),
                'group_id' => (int)$this->request->post('group'),
                'branche_id' => (int)$this->request->post('branch'),
                'company_id' => (int)$this->request->post('company'),
                'settlement_id' => (int)$settlement_std->id
            );

            $loan_type_groups = $this->GroupLoanTypes->get_loantype_groups((int)$loan_type);
            $loan = $this->Loantypes->get_loantype($loan_type);

            $record = array();

            foreach ($loan_type_groups as $loantype_group) {
                if ($loantype_group['id'] == $order['group_id']) {
                    $record = $loantype_group;
                }
            }

            if ($profunion == 0) {
                $order['percent'] = (float)$record['standart_percents'];
            } else {
                $order['percent'] = (float)$record['preferential_percents'];
            }

            $rest_sum = $order['amount'];
            $start_date = date('Y-m-d', strtotime($order['probably_start_date']));
            $end_date = new DateTime(date('Y-m-10', strtotime($order['probably_return_date'])));
            $issuance_date = new DateTime(date('Y-m-d', strtotime($start_date)));
            $paydate = new DateTime(date('Y-m-10', strtotime($start_date)));

            $percent_per_month = (($order['percent'] / 100) * 365) / 12;
            $percent_per_month = round($percent_per_month, 7);
            $annoouitet_pay = $order['amount'] * ($percent_per_month / (1 - pow((1 + $percent_per_month), -$loan->max_period)));
            $annoouitet_pay = round($annoouitet_pay, '2');

            if (date('d', strtotime($start_date)) < 10) {
                if ($issuance_date > $start_date && date_diff($paydate, $issuance_date)->days < 3) {
                    $plus_loan_percents = ($order['percent'] / 100) * $order['amount'] * date_diff($paydate, $issuance_date)->days;
                    $sum_pay = $annoouitet_pay + $plus_loan_percents;
                    $loan_percents_pay = ($rest_sum * $percent_per_month) + $plus_loan_percents;
                    $body_pay = $sum_pay - $loan_percents_pay;
                    $paydate->add(new DateInterval('P1M'));
                    $paydate = $this->check_pay_date($paydate);
                } else {
                    $sum_pay = ($order['percent'] / 100) * $order['amount'] * date_diff($paydate, $issuance_date)->days;
                    $loan_percents_pay = $sum_pay;
                    $body_pay = 0;
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
            } else {
                $issuance_date = new DateTime(date('Y-m-d', strtotime($start_date)));
                $first_pay = new DateTime(date('Y-m-10', strtotime($start_date . '+1 month')));
                $count_days_this_month = date('t', strtotime($issuance_date->format('Y-m-d')));
                $paydate = $this->check_pay_date($first_pay);

                if (date_diff($first_pay, $issuance_date)->days < 20) {
                    $sum_pay = ($order['percent'] / 100) * $order['amount'] * date_diff($first_pay, $issuance_date)->days;
                    $percents_pay = $sum_pay;
                }
                if (date_diff($first_pay, $issuance_date)->days >= 20 && date_diff($first_pay, $issuance_date)->days < $count_days_this_month) {
                    $minus_percents = ($order['percent'] / 100) * $order['amount'] * ($count_days_this_month - date_diff($first_pay, $issuance_date)->days);

                    $sum_pay = $annoouitet_pay - $minus_percents;
                    $percents_pay = ($rest_sum * $percent_per_month) - $minus_percents;
                    $body_pay = $sum_pay - $percents_pay;
                }
                if (date_diff($first_pay, $issuance_date)->days >= $count_days_this_month) {
                    $sum_pay = $annoouitet_pay;
                    $percents_pay = $rest_sum * $percent_per_month;
                    $body_pay = $sum_pay - $percents_pay;
                }

                $payment_schedule[$paydate->format('d.m.Y')] =
                    [
                        'pay_sum' => $sum_pay,
                        'loan_percents_pay' => $percents_pay,
                        'loan_body_pay' => ($body_pay) ? $body_pay : 0,
                        'comission_pay' => 0.00,
                        'rest_pay' => $rest_sum -= $body_pay
                    ];

                $paydate->add(new DateInterval('P1M'));
            }

            if ($rest_sum !== 0) {
                $paydate->setDate($paydate->format('Y'), $paydate->format('m'), 10);
                $interval = new DateInterval('P1M');
                $lastdate = clone $end_date;
                $end_date->setTime(0, 0, 1);
                $daterange = new DatePeriod($paydate, $interval, $end_date);

                foreach ($daterange as $date) {
                    $date = $this->check_pay_date($date);

                    if($date == $lastdate){
                        $loan_body_pay = $rest_sum;
                        $loan_percents_pay = $annoouitet_pay - $loan_body_pay;
                        $rest_sum = 0.00;
                    }else{
                        $loan_percents_pay = round($rest_sum * $percent_per_month, 2);
                        $loan_body_pay = round($annoouitet_pay - $loan_percents_pay, 2);
                        $rest_sum = round($rest_sum - $loan_body_pay, 2);
                    }

                    $payment_schedule[$date->format('d.m.Y')] =
                        [
                            'pay_sum' => $annoouitet_pay,
                            'loan_percents_pay' => $loan_percents_pay,
                            'loan_body_pay' => $loan_body_pay,
                            'comission_pay' => 0.00,
                            'rest_pay' => $rest_sum
                        ];
                }
            }

            $payment_schedule['result'] =
                [
                    'all_sum_pay' => 0.00,
                    'all_loan_percents_pay' => 0.00,
                    'all_loan_body_pay' => 0.00,
                    'all_comission_pay' => 0.00,
                    'all_rest_pay_sum' => 0.00
                ];

            $dates[0] = date('d.m.Y', strtotime($this->request->post('start_date')));
            $payments[0] = -$amount;

            foreach ($payment_schedule as $date => $pay) {
                if ($date != 'result') {
                    $payments[] = round($pay['pay_sum'], '2');
                    $dates[] = date('d.m.Y', strtotime($date));
                    $payment_schedule['result']['all_sum_pay'] += round($pay['pay_sum'], '2');
                    $payment_schedule['result']['all_loan_percents_pay'] += round($pay['loan_percents_pay'], '2');
                    $payment_schedule['result']['all_loan_body_pay'] += round($pay['loan_body_pay'], 2);
                    $payment_schedule['result']['all_comission_pay'] += round($pay['comission_pay'], '2');
                    $payment_schedule['result']['all_rest_pay_sum'] = 0.00;
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

            $order['psk'] = round(((pow((1 + $xirr), (1 / 12)) - 1) * 12) * 100, 3);

            $order['payment_schedule'] = json_encode($payment_schedule);

            $company = $this->Companies->get_company($order['company_id']);
            $group = $this->Groups->get_group($order['group_id']);

            $loan_type_number = ($loan_type < 10) ? '0' . $loan_type : $loan_type;

            if (isset($user['personal_number'])) {
                $personal_number = $user['personal_number'];
                $last_number = $this->orders->last_order_number($user_id);

                $uid = "$group->number $company->number $loan_type_number $personal_number";

                if ($last_number && $last_number < 10) {
                    $last_number += 1;
                    $uid .= '0' . $last_number;
                }

                if ($last_number == false) {
                    $uid .= ' 01';
                }
                if ($last_number && $last_number > 10) {
                    $last_number += 1;
                    $uid .= "$last_number";
                }

                $order['uid'] = $uid;
            }

            if (!empty((int)$this->request->post('order_id'))) {
                $order_id = (int)$this->request->post('order_id');
                $this->orders->update_order($order_id, $order);

                if ($this->request->post('create_new_order')) {
                    header("Location: " . $this->config->root_url . '/offline_order/' . $order_id);
                    exit;
                }
            } else {
                try {
                    $order_id = $this->orders->add_order($order);
                    response_json(['success' => 1, 'reason' => 'Заявка создана успешно', 'redirect' => $this->config->root_url . '/offline_order/' . $order_id]);
                } catch (Exception $exception) {
                    response_json(['error' => 1, 'reason' => 'Создать заявку не удалось']);
                }
            }
        }

        $this->design->assign('order', (object)$user);
    }

    private function action_edit_phone()
    {
        $phone = $this->request->post('phone');
        if (empty($phone)) {
            echo json_encode(['error' => 1]);
            exit;
        }
        $code = random_int(1000, 9999);
        $response = $this->sms->send(
            $phone,
            $code
        );
        $this->db->query('
        INSERT INTO s_sms_messages
        SET phone = ?, code = ?, response = ?, ip = ?, created = ?
        ', $phone, $code, $response['resp'], $_SERVER['REMOTE_ADDR'] ?? '', date('Y-m-d H:i:s'));
        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_edit_phone_with_code()
    {
        $phone= $this->request->post('phone');
        $code = $this->request->post('code');

        $this->db->query("
        SELECT code, created
        FROM s_sms_messages
        WHERE phone = ?
        AND code = ?
        ORDER BY created DESC
        LIMIT 1
        ", $phone, $code);
        $results = $this->db->results();
        if (empty($results)) {
            echo json_encode(['error' => 1]);
            exit;
        }
        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_edit_email()
    {
        $email = $this->request->post('email');
        if (empty($email)) {
            echo json_encode(['error' => 1, 'reason' => 'Не введён email']);
            exit;
        }
        $code = random_int(1000, 9999);

        $result = $this->db->query('
        INSERT INTO s_email_messages
        SET email = ?, code = ?, created = ?
        ', $email, $code, date('Y-m-d H:i:s'));

        $mailService = new MailService($this->config->mailjet_api_key, $this->config->mailjet_api_secret);
        $mailResponse = $mailService->send(
            'rucred@ucase.live',
            $email,
            'RuCred | Ваш проверочный код для смены почты',
            'Введите этот код в поле для проверки почты: ' . $code,
            '<h1>Введите этот код в поле для проверки почты:</h1>' . "<h2>$code</h2>"
        );

        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_edit_email_with_code()
    {
        $email = $this->request->post('email');
        $code = $this->request->post('code');

        $this->db->query("
        SELECT code, created
        FROM s_email_messages
        WHERE email = ?
        AND code = ?
        ORDER BY created DESC
        LIMIT 1
        ", $email, $code);
        $results = $this->db->results();
        if (empty($results)) {
            echo json_encode(['error' => 1]);
            exit;
        }
        echo json_encode(['success' => 1]);
        exit;
    }

    private function check_date()
    {
        $start_date = $this->request->get('start_date');
        $loan_id = $this->request->get('loan_id');

        $loan = $this->Loantypes->get_loantype($loan_id);

        $start_date = date('Y-m-d', strtotime($start_date));
        $first_pay = new DateTime(date('Y-m-10', strtotime($start_date)));
        $end_date = date('Y-m-10', strtotime($start_date . '+' . $loan->max_period . 'month'));

        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);

        if ($start_date > $first_pay) {
            $first_pay->add(new DateInterval('P1M'));
        }

        $first_pay = $this->check_pay_date($first_pay);

        if (date_diff($first_pay, $start_date)->days < 20 && $first_pay->format('m') != $start_date->format('m')) {
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
}
