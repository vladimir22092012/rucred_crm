<?php

use App\Services\MailService;

error_reporting(-1);
ini_set('display_errors', 'Off');
date_default_timezone_set('Europe/Moscow');

class NeworderController extends Controller
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

        if ($this->request->get('order_id')) {
            $order_id = $this->request->get('order_id');

            $order = $this->orders->get_order($order_id);

            if (!empty($order->faktaddress_id)) {
                $Faktaddressfull = $this->Addresses->get_address($order->faktaddress_id);
                $this->design->assign('Faktaddressfull', $Faktaddressfull);
            }

            if (!empty($order->regaddress_id)) {
                $Regaddressfull = $this->Addresses->get_address($order->regaddress_id);
                $this->design->assign('Regaddressfull', $Regaddressfull);
            }

            $order->requisite = $this->requisites->get_requisite($order->requisite_id);

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

            if ($this->request->get('patronymic'))
                $user['patronymic'] = $this->request->get('patronymic');

            $user['birth'] = date('d.m.Y', strtotime($this->request->get('birth')));

            $users = $this->users->check_exist_users($user);

            if (!empty($users))
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

        $settlements = $this->OrganisationSettlements->get_settlements();
        $this->design->assign('settlements', $settlements);

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

        $draft = ($this->request->post('draft')) ? 1 : 0;

        $user = array();

        $user['user_id'] = intval($this->request->post('user_id'));

        $user['firstname'] = trim($this->request->post('firstname'));

        if (empty($user['firstname'])) {
            response_json(['error' => 1, 'reason' => 'Отсутствует имя']);
            exit;
        }


        $user['lastname'] = trim($this->request->post('lastname'));

        if (empty($user['firstname'])) {
            response_json(['error' => 1, 'reason' => 'Отсутствует фамилия']);
            exit;
        }

        $user['patronymic'] = trim($this->request->post('patronymic'));

        $requisite = $this->request->post('requisite');

        $requisite['holder'] = $requisite['holder']['lastname'] . '' . $requisite['holder']['firstname'] . '' . $requisite['holder']['patronymic'];
        $requisite['holder'] = trim($requisite['holder']);

        if (empty($requisite['name']) || empty($requisite['bik']) || empty($requisite['number']) || empty($requisite['holder']) || empty($requisite['correspondent_acc'])) {
            response_json(['error' => 1, 'reason' => 'Заполните корректно реквизиты']);
            exit;
        }

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

        if ($this->request->post('no_attestation') == 1) {
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
        $user['phone_mobile'] = preg_replace('/[^0-9]/', '', $user['phone_mobile']);

        if (empty($user['phone_mobile'])) {
            response_json(['error' => 1, 'reason' => 'Отсутствует номер телефона']);
            exit;
        }

        $user['phone_mobile_confirmed'] = (int)$this->request->post('phone_confirmed');

        if ($this->request->post('viber_same') == 1) {
            $user['viber_num'] = $user['phone_mobile'];
        } else {
            $user['viber_num'] = trim((string)$this->request->post('viber'));
        }

        if ($this->request->post('whatsapp_same') == 1) {
            $user['whatsapp_num'] = $user['phone_mobile'];
        } else {
            $user['whatsapp_num'] = trim((string)$this->request->post('whatsapp'));
        }

        if ($this->request->post('telegram_same') == 1) {
            $user['telegram_num'] = $user['phone_mobile'];
        } else {
            $user['telegram_num'] = trim((string)$this->request->post('telegram'));
        }


        $user['email'] = trim((string)$this->request->post('email'));
        $user['gender'] = trim((string)$this->request->post('gender'));
        $user['birth'] = trim((string)$this->request->post('birth'));
        $user['birth_place'] = trim((string)$this->request->post('birth_place'));

        if (empty($user['birth_place'])) {
            response_json(['error' => 1, 'reason' => 'Отсутствует место рождения']);
            exit;
        }

        if (empty($user['email'])) {
            response_json(['error' => 1, 'reason' => 'Отсутствует электронная почта']);
            exit;
        }

        $user['push_not'] = (int)$this->request->post('push_not');
        $user['sms_not'] = (int)$this->request->post('sms_not');
        $user['email_not'] = (int)$this->request->post('email_not');
        $user['massanger_not'] = (int)$this->request->post('massanger_not');


        $passport_serial = (string)$this->request->post('passport_serial');
        $passport_number = (string)$this->request->post('passport_number');

        $user['passport_serial'] = "$passport_serial $passport_number";

        $user['snils'] = (string)$this->request->post('snils');
        $user['inn'] = (string)$this->request->post('inn');

        if (empty($user['snils'])) {
            response_json(['error' => 1, 'reason' => 'Отсутствует снилс']);
            exit;
        }

        if (empty($user['inn'])) {
            response_json(['error' => 1, 'reason' => 'Отсутствует инн']);
            exit;
        }

        $user['passport_date'] = (string)$this->request->post('passport_date');
        $user['passport_issued'] = (string)$this->request->post('passport_issued');
        $user['subdivision_code'] = (string)$this->request->post('subdivision_code');

        if (empty($user['passport_serial']) || empty($user['passport_date']) || empty($user['passport_issued']) || empty($user['subdivision_code'])) {
            response_json(['error' => 1, 'reason' => 'Заполните паспортные данные']);
            exit;
        }

        $user['income'] = preg_replace("/[^,.0-9]/", '', $this->request->post('income_medium'));
        $user['expenses'] = preg_replace("/[^,.0-9]/", '', $this->request->post('outcome_medium'));

        if (empty($user['income'])) {
            response_json(['error' => 1, 'reason' => 'Отсутствует среднемесячный доход']);
            exit;
        }

        if (empty($user['expenses'])) {
            response_json(['error' => 1, 'reason' => 'Отсутствует среднемесячный расход']);
            exit;
        }

        $user['loan_history'] = '[]';

        $Regadress = json_decode($this->request->post('Regadress'));

        $regaddress = [];
        $regaddress['adressfull'] = $this->request->post('Regadressfull');
        $regaddress['zip'] = $Regadress->data->postal_code ?? '';
        $regaddress['region'] = $Regadress->data->region ?? '';
        $regaddress['region_type'] = $Regadress->data->region_type ?? '';
        $regaddress['city'] = $Regadress->data->city ?? '';
        $regaddress['city_type'] = $Regadress->data->city_type ?? '';
        $regaddress['district'] = $Regadress->data->city_district ?? '';
        $regaddress['district_type'] = $Regadress->data->city_district_type ?? '';
        $regaddress['locality'] = $Regadress->data->settlement ?? '';
        $regaddress['locality_type'] = $Regadress->data->settlement_type ?? '';
        $regaddress['street'] = $Regadress->data->street ?? '';
        $regaddress['street_type'] = $Regadress->data->street_type ?? '';
        $regaddress['house'] = $Regadress->data->house ?? '';
        $regaddress['building'] = $Regadress->data->block ?? '';
        $regaddress['room'] = $Regadress->data->flat ?? '';
        $regaddress['okato'] = $Regadress->data->okato ?? '';
        $regaddress['oktmo'] = $Regadress->data->oktmo ?? '';


        if (empty($regaddress['adressfull'])) {
            response_json(['error' => 1, 'reason' => 'Отсутствует адрес регистрации']);
            exit;
        }

        if ($this->request->post('actual_address') == 1) {
            $user['actual_address'] = 1;
            $faktaddress = $regaddress;
        } else {
            $user['actual_address'] = 0;
            $Fakt_adress = json_decode($this->request->post('Fakt_adress'));

            $faktaddress = [];
            $faktaddress['adressfull'] = $this->request->post('Faktadressfull');
            $faktaddress['zip'] = $Fakt_adress->data->postal_code ?? '';
            $faktaddress['region'] = $Fakt_adress->data->region ?? '';
            $faktaddress['region_type'] = $Fakt_adress->data->region_type ?? '';
            $faktaddress['city'] = $Fakt_adress->data->city ?? '';
            $faktaddress['city_type'] = $Fakt_adress->data->city_type ?? '';
            $faktaddress['district'] = $Fakt_adress->data->city_district ?? '';
            $faktaddress['district_type'] = $Fakt_adress->data->city_district_type ?? '';
            $faktaddress['locality'] = $Fakt_adress->data->settlement ?? '';
            $faktaddress['locality_type'] = $Fakt_adress->data->settlement_type ?? '';
            $faktaddress['street'] = $Fakt_adress->data->street ?? '';
            $faktaddress['street_type'] = $Fakt_adress->data->street_type ?? '';
            $faktaddress['house'] = $Fakt_adress->data->house ?? '';
            $faktaddress['building'] = $Fakt_adress->data->block ?? '';
            $faktaddress['room'] = $Fakt_adress->data->flat ?? '';
            $faktaddress['okato'] = $Fakt_adress->data->okato ?? '';
            $faktaddress['oktmo'] = $Fakt_adress->data->oktmo ?? '';

            if (empty($faktaddress['adressfull'])) {
                response_json(['error' => 1, 'reason' => 'Отсутствует фактический адрес проживания']);
                exit;
            }
        }

        $user['company_id'] = (int)$this->request->post('company', 'integer');
        $user['group_id'] = (int)$this->request->post('group', 'integer');
        $user['branche_id'] = (int)$this->request->post('branch', 'integer');

        if (empty($user['company_id'])) {
            response_json(['error' => 1, 'reason' => 'Отсутствует компания']);
            exit;
        }

        if (empty($user['group_id'])) {
            response_json(['error' => 1, 'reason' => 'Отсутствует группа']);
            exit;
        }


        if (empty($user['user_id'])) {
            $user['stage_registration'] = '7';
            unset($user['user_id']);

            $last_personal_number = $this->users->last_personal_number();

            $user['personal_number'] = $last_personal_number + 1;
            $user['original'] = 1;

            if ($user['user_id'] = $this->users->add_user($user)) {
                $user['regaddress_id'] = $this->Addresses->add_address($regaddress);

                $user['faktaddress_id'] = $this->Addresses->add_address($faktaddress);
            } else {
                $this->design->assign('error', 'Не удалось создать клиента');
            }
        }

        $loan_type = $this->request->post('loan_type_to_submit');

        if (!empty($user['user_id'])) {
            $old_user = $this->users->get_user($user['user_id']);
            $user_id = $user['user_id'];
            unset($user['user_id']);

            $user['original'] = 1;

            if (!empty($old_user->regaddress_id))
                $this->Addresses->update_address($old_user->regaddress_id, $regaddress);
            else
                $user['regaddress_id'] = $this->Addresses->add_address($regaddress);

            if (!empty($old_user->faktaddress_id))
                $this->Addresses->update_address($old_user->faktaddress_id, $faktaddress);
            else
                $user['faktaddress_id'] = $this->Addresses->add_address($faktaddress);

            $this->users->update_user($user_id, $user);


            if (empty($requisite['id'])) {
                unset($requisite['id']);
                $requisite['user_id'] = $user_id;
                $requisite['id'] = $this->requisites->add_requisite($requisite);
            } else {
                $this->requisites->update_requisite($requisite['id'], $requisite);
            }

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
                'status' => ($draft == 1) ? 12 : 1,
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
                'settlement_id' => (int)$this->request->post('settlement'),
                'requisite_id' => $requisite['id'],
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

            if (empty($user['branche_id'])) {
                $branches = $this->Branches->get_branches(['group_id' => $user['group_id']]);

                foreach ($branches as $branch) {
                    if ($branch->number == '00') {
                        $first_pay_day = $branch->payday;
                        $user['branche_id'] = $branch->id;
                    }
                }
            } else {
                $branch = $this->Branches->get_branch($user['branche_id']);
                $first_pay_day = $branch->payday;
            }

            $rest_sum = $order['amount'];
            $start_date = date('Y-m-d', strtotime($order['probably_start_date']));
            $end_date = new DateTime(date('Y-m-' . $first_pay_day, strtotime($order['probably_return_date'])));
            $issuance_date = new DateTime(date('Y-m-d', strtotime($start_date)));
            $paydate = new DateTime(date('Y-m-' . "$first_pay_day", strtotime($start_date)));

            $percent_per_month = (($order['percent'] / 100) * 365) / 12;
            $annoouitet_pay = $order['amount'] * ($percent_per_month / (1 - pow((1 + $percent_per_month), -$loan->max_period)));
            $annoouitet_pay = round($annoouitet_pay, '2');

            if (date('d', strtotime($start_date)) < $first_pay_day) {
                if ($issuance_date > $start_date && date_diff($paydate, $issuance_date)->days < $loan->free_period) {
                    $plus_loan_percents = round(($order['percent'] / 100) * $order['amount'] * date_diff($paydate, $issuance_date)->days, 2);
                    $sum_pay = $annoouitet_pay + $plus_loan_percents;
                    $loan_percents_pay = round(($rest_sum * $percent_per_month) + $plus_loan_percents, 2, PHP_ROUND_HALF_DOWN);
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
                $first_pay = new DateTime(date('Y-m-' . $first_pay_day, strtotime($start_date . '+1 month')));
                $count_days_this_month = date('t', strtotime($issuance_date->format('Y-m-d')));
                $paydate = $this->check_pay_date($first_pay);

                if (date_diff($first_pay, $issuance_date)->days <= $loan->min_period) {
                    $sum_pay = ($order['percent'] / 100) * $order['amount'] * date_diff($first_pay, $issuance_date)->days;
                    $percents_pay = $sum_pay;
                }
                if (date_diff($first_pay, $issuance_date)->days > $loan->min_period && date_diff($first_pay, $issuance_date)->days < $count_days_this_month) {
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
                $paydate->setDate($paydate->format('Y'), $paydate->format('m'), $first_pay_day);
                $interval = new DateInterval('P1M');
                $lastdate = clone $end_date;
                $end_date->setTime(0, 0, 1);
                $daterange = new DatePeriod($paydate, $interval, $end_date);

                foreach ($daterange as $date) {
                    $date = $this->check_pay_date($date);

                    if ($date->format('m') == $lastdate->format('m')) {
                        $loan_body_pay = $rest_sum;
                        $loan_percents_pay = $annoouitet_pay - $loan_body_pay;
                        $rest_sum = 0.00;
                    } else {
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

            $new_schedule[date('Y-m-d')] = $payment_schedule;
            $order['payment_schedule'] = json_encode($new_schedule);

            $company = $this->Companies->get_company($order['company_id']);
            $group = $this->Groups->get_group($order['group_id']);

            $loan_type_number = ($loan_type < 10) ? '0' . $loan_type : $loan_type;

            if (isset($user['personal_number'])) {
                $personal_number = $user['personal_number'];

            }

            if (isset($user_id)) {
                $user = $this->users->get_user($user_id);
                $personal_number = $user->personal_number;
            }

            $orders = $this->orders->get_orders(['user_id' => $user_id]);

            if (!empty($count_orders)) {
                $count_orders = count($orders);
                str_pad($count_orders, 2, '0', STR_PAD_LEFT);
            } else {
                $count_orders = '01';
            }
            $order['uid'] = "$group->number $company->number $loan_type_number $personal_number $count_orders";

            if (($this->request->get('order_id'))) {
                $order_id = $this->request->get('order_id');
                $this->orders->update_order($order_id, $order);

                if ($draft == 1) {

                    response_json(['success' => 1, 'reason' => 'Заявка создана успешно', 'redirect' => $this->config->root_url . '/drafts/']);
                    exit;
                } else {

                    $order = $this->orders->get_order($order_id);

                    $ticket =
                        [
                            'creator' => $this->manager->id,
                            'creator_company' => 2,
                            'client_lastname' => $order->lastname,
                            'client_firstname' => $order->firstname,
                            'client_patronymic' => $order->patronymic,
                            'head' => 'Новая заявка',
                            'text' => 'Ознакомьтесь с новой заявкой и верифицируйте своего сотрудника и верифицируйте своего сотрудника',
                            'company_id' => $order->company_id,
                            'group_id' => $order->group_id,
                            'order_id' => $order_id,
                            'status' => 0
                        ];

                    $ticket_id = $this->Tickets->add_ticket($ticket);
                    $message =
                        [
                            'message' => 'Ознакомьтесь с новой заявкой и верифицируйте своего сотрудника',
                            'ticket_id' => $ticket_id,
                            'manager_id' => $this->manager->id,
                        ];
                    $this->TicketMessages->add_message($message);
                    response_json(['success' => 1, 'reason' => 'Заявка создана успешно', 'redirect' => $this->config->root_url . '/offline_order/' . $order_id]);
                    exit;
                }
            } else {

                $check_same_users = $this->request->post('check_same_users');

                if (empty($check_same_users)) {
                    response_json(['error' => 1, 'reason' => 'Произведите проверку на совпадения']);
                    exit;
                }

                $order_id = $this->orders->add_order($order);

                if ($draft == 1) {
                    // запускаем бесплатные скоринги
                    $scoring_types = $this->scorings->get_types();
                    foreach ($scoring_types as $scoring_type) {
                        if (empty($scoring_type->is_paid)) {
                            $add_scoring = array(
                                'user_id' => $user_id,
                                'order_id' => $order_id,
                                'type' => $scoring_type->name,
                                'status' => 'new',
                                'start_date' => date('Y-m-d H:i:s'),
                            );
                            $this->scorings->add_scoring($add_scoring);
                        }
                    }

                    response_json(['success' => 1, 'reason' => 'Заявка создана успешно', 'redirect' => $this->config->root_url . '/neworder/draft/' . $order_id]);
                } else {
                    try {
                        $user = $this->users->get_user($order['user_id']);

                        $ticket =
                            [
                                'creator' => $this->manager->id,
                                'creator_company' => 2,
                                'client_lastname' => $user->lastname,
                                'client_firstname' => $user->firstname,
                                'client_patronymic' => $user->patronymic,
                                'head' => 'Новая заявка',
                                'text' => 'Ознакомьтесь с новой заявкой и верифицируйте своего сотрудника',
                                'company_id' => $order['company_id'],
                                'group_id' => $order['group_id'],
                                'order_id' => $order_id,
                                'status' => 0
                            ];

                        $ticket_id = $this->Tickets->add_ticket($ticket);

                        $message =
                            [
                                'message' => 'Ознакомьтесь с новой заявкой и верифицируйте своего сотрудника',
                                'ticket_id' => $ticket_id,
                                'manager_id' => $this->manager->id,
                            ];

                        $this->TicketMessages->add_message($message);

                        // запускаем бесплатные скоринги
                        $scoring_types = $this->scorings->get_types();
                        foreach ($scoring_types as $scoring_type) {
                            if (empty($scoring_type->is_paid)) {
                                $add_scoring = array(
                                    'user_id' => $order['user_id'],
                                    'order_id' => $order_id,
                                    'type' => $scoring_type->name,
                                    'status' => 'new',
                                    'start_date' => date('Y-m-d H:i:s'),
                                );
                                $this->scorings->add_scoring($add_scoring);
                            }
                        }

                        response_json(['success' => 1, 'reason' => 'Заявка создана успешно', 'redirect' => $this->config->root_url . '/offline_order/' . $order_id]);
                    } catch (Exception $exception) {
                        response_json(['error' => 1, 'reason' => 'Создать заявку не удалось']);
                    }
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
        $message = "Ваш код подтверждения телефона:  $code. Сообщите код андеррайтеру РуКреда";
        $response = $this->sms->send(
            $phone,
            $message
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
        $phone = $this->request->post('phone');
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
            'Ваш код подтверждения электронной почты: ' . $code,
            '<h1>Сообщите код андеррайтеру РуКреда: </h1>' . "<h2>$code</h2>"
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
        $company_id = $this->request->get('company_id');

        if (empty($user['branche_id'])) {
            $branches = $this->Branches->get_branches(['company_id' => $company_id]);

            foreach ($branches as $branch) {
                if ($branch->number == '00')
                    $first_pay_day = $branch->payday;
            }
        } else {
            $branch = $this->Branches->get_branch($user['branche_id']);
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

    private function action_sum_to_pay()
    {
        $loan_id = $this->request->post('loan_id');
        $amount = $this->request->post('amount');
        $amount = str_replace(' ', '', $amount);
        $date_from = date('Y-m-d', strtotime($this->request->post('date_from')));
        $date_to = date('Y-m-d', strtotime($this->request->post('date_to')));
        $branch_id = $this->request->post('branch_id');
        $group_id = $this->request->post('group_id');
        $percent = $this->request->post('percents');

        $loan = $this->Loantypes->get_loantype($loan_id);

        if (empty($branch_id) || $branch_id == 'none') {
            $branches = $this->Branches->get_branches(['group_id' => $group_id]);

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
        $start_date = $date_from;
        $end_date = new DateTime(date('Y-m-' . $first_pay_day, strtotime($date_to)));
        $issuance_date = new DateTime(date('Y-m-d', strtotime($start_date)));
        $paydate = new DateTime(date('Y-m-' . "$first_pay_day", strtotime($start_date)));

        $percent_per_month = (($percent / 100) * 360) / 12;
        $annoouitet_pay = $amount * ($percent_per_month / (1 - pow((1 + $percent_per_month), -1)));
        $annoouitet_pay = round($annoouitet_pay, '2');

        if ($loan_id == 1) {

            if (date('d', strtotime($start_date)) < $first_pay_day) {
                if ($issuance_date > $start_date && date_diff($paydate, $issuance_date)->days < $loan->free_period) {
                    $plus_loan_percents = round(($percent / 100) * $amount * date_diff($paydate, $issuance_date)->days, 2);
                    $sum_pay = $annoouitet_pay + $plus_loan_percents;
                    $loan_percents_pay = round(($rest_sum * $percent_per_month) + $plus_loan_percents, 2, PHP_ROUND_HALF_DOWN);
                    $body_pay = $sum_pay - $loan_percents_pay;
                    $paydate->add(new DateInterval('P1M'));
                    $paydate = $this->check_pay_date($paydate);
                } else {
                    $sum_pay = ($percent / 100) * $amount * date_diff($paydate, $issuance_date)->days;
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
                $first_pay = new DateTime(date('Y-m-' . $first_pay_day, strtotime($start_date . '+1 month')));
                $count_days_this_month = date('t', strtotime($issuance_date->format('Y-m-d')));
                $paydate = $this->check_pay_date($first_pay);

                if (date_diff($first_pay, $issuance_date)->days <= $loan->min_period) {
                    $sum_pay = ($percent / 100) * $amount * date_diff($first_pay, $issuance_date)->days;
                    $loan_percents_pay = $sum_pay;
                }
                if (date_diff($first_pay, $issuance_date)->days > $loan->min_period && date_diff($first_pay, $issuance_date)->days < $count_days_this_month) {
                    $minus_percents = ($percent / 100) * $amount * ($count_days_this_month - date_diff($first_pay, $issuance_date)->days);

                    $sum_pay = $annoouitet_pay - $minus_percents;
                    $loan_percents_pay = ($rest_sum * $percent_per_month) - $minus_percents;
                    $body_pay = $sum_pay - $loan_percents_pay;
                }
                if (date_diff($first_pay, $issuance_date)->days >= $count_days_this_month) {
                    $sum_pay = $annoouitet_pay;
                    $loan_percents_pay = $rest_sum * $percent_per_month;
                    $body_pay = $sum_pay - $loan_percents_pay;
                }

                $payment_schedule[$paydate->format('d.m.Y')] =
                    [
                        'pay_sum' => $sum_pay,
                        'loan_percents_pay' => $loan_percents_pay,
                        'loan_body_pay' => ($body_pay) ? $body_pay : 0,
                        'comission_pay' => 0.00,
                        'rest_pay' => $rest_sum -= $body_pay
                    ];

                $paydate->add(new DateInterval('P1M'));
            }

            if ($rest_sum !== 0) {
                $paydate->setDate($paydate->format('Y'), $paydate->format('m'), $first_pay_day);
                $interval = new DateInterval('P1M');
                $end_date->setTime(0, 0, 1);
                $daterange = new DatePeriod($paydate, $interval, $end_date);

                foreach ($daterange as $date) {
                    $date = $this->check_pay_date($date);
                    $start_date = new DateTime($start_date);

                    $count_days = date_diff($start_date, $date)->days;
                    $loan_body_pay = $rest_sum;
                    $loan_percents_pay = ((($percent * $amount) * $count_days) / 100) - $loan_percents_pay;
                    $pay_sum = $loan_body_pay + $loan_percents_pay;
                    $rest_sum = 0.00;

                    $date = ($date)->format('d.m.Y');

                    $payment_schedule[$date] =
                        [
                            'pay_sum' => $pay_sum,
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

            foreach ($payment_schedule as $date => $pay) {
                if ($date != 'result') {
                    $payment_schedule['result']['all_sum_pay'] += round($pay['pay_sum'], '2');
                    $payment_schedule['result']['all_loan_percents_pay'] += round($pay['loan_percents_pay'], '2');
                    $payment_schedule['result']['all_loan_body_pay'] += round($pay['loan_body_pay'], 2);
                    $payment_schedule['result']['all_comission_pay'] += round($pay['comission_pay'], '2');
                    $payment_schedule['result']['all_rest_pay_sum'] = 0.00;
                }
            }

            echo json_encode($payment_schedule['result']['all_sum_pay']);
            exit;
        } else {
            echo json_encode($annoouitet_pay);
            exit;
        }
    }
}
