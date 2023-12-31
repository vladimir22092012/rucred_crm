<?php

use Telegram\Bot\Api;
use Viber\Bot;
use Viber\Api\Sender;
use Viber\Client;
use App\Services\MailService;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

error_reporting(-1);
ini_set('display_errors', 'Off');

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

            $telegram_confirmed = $this->TelegramUsers->get($order->user_id, 0);
            $viber_confirmed = $this->ViberUsers->get($order->user_id, 0);

            if (!empty($telegram_confirmed))
                $this->design->assign('telegram_confirmed', '1');

            if (!empty($viber_confirmed))
                $this->design->assign('viber_confirmed', '1');

            if (!empty($order->faktaddress_id)) {
                $Faktaddressfull = $this->Addresses->get_address($order->faktaddress_id);
                $this->design->assign('Faktaddressfull', $Faktaddressfull);
            }

            if (!empty($order->regaddress_id)) {
                $Regaddressfull = $this->Addresses->get_address($order->regaddress_id);
                $this->design->assign('Regaddressfull', $Regaddressfull);
            }

            if (!empty($order->requisite_id)) {
                $order->requisite = $this->requisites->get_requisite($order->requisite_id);

                if (!empty($order->requisite)) {
                    list($holder_lastname, $holder_firstname, $holder_patronymic) = explode(' ', $order->requisite->holder);

                    $this->design->assign('holder_lastname', $holder_lastname);
                    $this->design->assign('holder_firstname', $holder_firstname);
                    $this->design->assign('holder_patronymic', $holder_patronymic);
                }
            }

            if (!empty($order->card_id)) {
                $card = $this->cards->get_card($order->card_id);
                $order->card_name = $card->name;
                $order->pan = $card->pan;
                $order->expdate = $card->expdate;
            }

            if (!empty($order->passport_serial)) {
                $passport_serial = explode(' ', $order->passport_serial);
                $order->passport_serial = $passport_serial[0];
                $order->passport_number = $passport_serial[1];
            }

            if (!empty($order->fio_spouse)) {
                $fio_spouse = explode(' ', $order->fio_spouse);
                $this->design->assign('fio_spouse', $fio_spouse);
            }

            $this->design->assign('order', $order);
        }

        if ($this->request->get('start_date')) {
            $this->check_date();
        }

        if ($this->request->get('action') == 'get_companies') {
            $group_id = $this->request->get('group_id');

            $companies = $this->Companies->get_companies(['group_id' => $group_id, 'blocked' => 0, 'permissions' => ['all', 'offline']]);
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

        if ($this->request->get('action') == 'check_same_users') {

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

        foreach ($groups as $key => $group) {
            if (in_array($group->blocked, ['online', 'nowhere']))
                unset($groups[$key]);

            $flag = $this->GroupLoanTypes->gets(['group_id' => $group->id, 'on_off_flag' => 1]);

            if (empty($flag))
                unset($groups[$key]);
        }

        $this->design->assign('groups', $groups);

        $settlements = $this->OrganisationSettlements->get_settlements();
        $this->design->assign('settlements', $settlements);

        return $this->design->fetch('offline/neworder.tpl');
    }


    private function action_create_new_order()
    {
        $amount = preg_replace("/[^,.0-9]/", '', $this->request->post('amount'));

        $percent = (float)$this->request->post('percent');
        $charge = (float)$this->request->post('charge');
        $insure = (float)$this->request->post('insure');
        $peni = (float)$this->request->post('peni');

        $this->design->assign('percent', $percent);
        $this->design->assign('charge', $charge);
        $this->design->assign('peni', $peni);
        $this->design->assign('amount', $amount);

        $draft = ($this->request->post('draft')) ? 1 : 0;

        $user = array();

        $user['user_id'] = intval($this->request->post('user_id'));

        $user['firstname'] = trim($this->request->post('firstname'));
        $user['canSendOnec'] = 1;
        $user['canSendYaDisk'] = 1;

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

        $payout_type = $this->request->post('payout_type');
        $requisite = $this->request->post('requisite');
        $settlement_id = (int)$this->request->post('settlement');

        if ($payout_type == 'bank' || $settlement_id == 3) {

            $requisite['holder'] = $requisite['holder']['lastname'] . ' ' . $requisite['holder']['firstname'] . ' ' . $requisite['holder']['patronymic'];
            $requisite['holder'] = trim($requisite['holder']);

            if (empty($requisite['name']) || empty($requisite['bik']) || empty($requisite['number']) || empty($requisite['holder']) || empty($requisite['correspondent_acc'])) {
                response_json(['error' => 1, 'reason' => 'Заполните корректно реквизиты']);
                exit;
            }
        }

        if ($payout_type == 'card') {
            $card_name = $this->request->post('card_name');
            $pan = $this->request->post('pan');
            $expdate = $this->request->post('expdate');

            if (empty($card_name) || empty($pan) || empty($expdate)) {
                response_json(['error' => 1, 'reason' => 'Заполните корректно данные по карте']);
                exit;
            }
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

        $credits_story = array_replace_recursive($credits_bank_name, $credits_rest_sum, $credits_month_pay, $credits_return_date, $credits_percents, $credits_delay);
        $cards_story = array_replace_recursive($cards_bank_name, $cards_limit, $cards_rest_sum, $cards_validity_period, $cards_delay);

        if ($this->request->post('no_attestation') == 1) {
            $attestations = null;
        }

        $user['attestation'] = $attestations;

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

        $all_sum_credits = 0;
        $sum_credits_pay = 0;

        if (!empty($credits_story)) {
            foreach ($credits_story as $credit) {
                $credit['credits_month_pay'] = preg_replace("/[^,.0-9]/", '', $credit['credits_month_pay']);
                $credit['credits_rest_sum'] = preg_replace("/[^,.0-9]/", '', $credit['credits_rest_sum']);

                if (!empty($credit['credits_month_pay']) && $credit['credits_delay'] == 'Нет')
                    $sum_credits_pay += $credit['credits_month_pay'];

                if (!empty($credit['credits_rest_sum']) && $credit['credits_delay'] == 'Да')
                    $sum_credits_pay += $credit['credits_rest_sum'];
            }

            $all_sum_credits += $sum_credits_pay;
        }

        if (!empty($cards_story)) {
            foreach ($cards_story as $card) {
                $card['cards_rest_sum'] = preg_replace("/[^,.0-9]/", '', $card['cards_rest_sum']);
                $card['cards_limit'] = preg_replace("/[^,.0-9]/", '', $card['cards_limit']);

                if (!empty($card['cards_limit'])) {
                    $max = 0.05 * $card['cards_limit'];
                } else {
                    $max = 0;
                }
                if (!empty($card['cards_rest_sum'])) {
                    $min = 0.1 * $card['cards_rest_sum'];
                } else {
                    $min = 0;
                }

                $all_sum_credits += min($max, $min);
            }
        }

        $user['credits_story'] = json_encode($credits_story);
        $user['cards_story'] = json_encode($cards_story);

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

        $user['push_not'] = (int)$this->request->post('push_not');
        $user['sms_not'] = (int)$this->request->post('sms_not');
        $user['email_not'] = (int)$this->request->post('email_not');
        $user['massanger_not'] = (int)$this->request->post('massanger_not');


        $user['phone_mobile'] = trim((string)$this->request->post('phone'));
        $user['phone_mobile'] = preg_replace('/[^0-9]/', '', $user['phone_mobile']);
        $user['dependents'] = $this->request->post('dependents');

        if (empty($user['dependents']))
            $user['dependents'] = 0;

        if (empty($user['phone_mobile'])) {
            response_json(['error' => 1, 'reason' => 'Отсутствует номер телефона']);
            exit;
        }

        if (empty($this->request->post('phone_confirmed'))) {
            response_json(['error' => 1, 'reason' => 'Номер телефона не подтвержден']);
            exit;
        }

        if (empty($this->request->post('email_confirmed'))) {
            response_json(['error' => 1, 'reason' => 'Почта не подтверждена']);
            exit;
        }

        $user['phone_mobile_confirmed'] = (int)$this->request->post('phone_confirmed');
        $user['email_confirmed'] = (int)$this->request->post('email_confirmed');

        if ($this->request->post('viber_same') == 1) {
            $user['viber_num'] = $user['phone_mobile'];
        } else {
            $user['viber_num'] = trim((string)$this->request->post('viber'));
        }

        /*
        if ($this->request->post('whatsapp_same') == 1) {
            $user['whatsapp_num'] = $user['phone_mobile'];
        } else {
            $user['whatsapp_num'] = trim((string)$this->request->post('whatsapp'));
        }
        */

        if ($this->request->post('telegram_same') == 1) {
            $user['telegram_num'] = $user['phone_mobile'];
        } else {
            $user['telegram_num'] = trim((string)$this->request->post('telegram'));
        }


        $user['email'] = trim((string)$this->request->post('email'));

        $user['gender'] = trim((string)$this->request->post('gender'));
        $user['birth'] = trim((string)$this->request->post('birth'));

        $now_date = new DateTime(date('Y-m-d'));
        $birth_date = new DateTime(date('Y-m-d', strtotime($user['birth'])));

        if ($birth_date > $now_date) {
            response_json(['error' => 1, 'reason' => 'Некорректная дата рождения']);
            exit;
        } else {
            if (date_diff($now_date, $birth_date)->y < 18) {
                response_json(['error' => 1, 'reason' => 'Не должно быть меньше 18 лет']);
                exit;
            }
        }

        $user['birth_place'] = trim((string)$this->request->post('birth_place'));

        if (empty($user['birth_place'])) {
            response_json(['error' => 1, 'reason' => 'Отсутствует место рождения']);
            exit;
        }

        if (empty($user['email'])) {
            response_json(['error' => 1, 'reason' => 'Отсутствует электронная почта']);
            exit;
        }

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

        $check_same_users = $this->request->post('check_same_users');

        if (empty($check_same_users)) {
            response_json(['error' => 1, 'reason' => 'Произведите проверку на совпадения']);
            exit;
        }

        $user['passport_date'] = (string)$this->request->post('passport_date');

        $now_date = new DateTime(date('Y-m-d'));
        $passport_date = new DateTime(date('Y-m-d', strtotime($user['passport_date'])));

        if ($passport_date > $now_date || date_diff($now_date, $passport_date)->days < 31) {
            response_json(['error' => 1, 'reason' => 'Некорректная дата выдачи паспорта']);
            exit;
        }

        $user['passport_issued'] = (string)$this->request->post('passport_issued');
        $user['subdivision_code'] = (string)$this->request->post('subdivision_code');

        if (empty($user['passport_serial']) || empty($user['passport_date']) || empty($user['passport_issued']) || empty($user['subdivision_code'])) {
            response_json(['error' => 1, 'reason' => 'Заполните паспортные данные']);
            exit;
        }

        if (strlen($user['subdivision_code'] > 6) && strlen($user['subdivision_code'] < 6)) {
            response_json(['error' => 1, 'reason' => 'Некорректный код подразделения']);
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

        $user['timezone'] = $this->request->post('timezone');

        if (empty($user['group_id'])) {
            response_json(['error' => 1, 'reason' => 'Отсутствует группа']);
            exit;
        }

        $user['regaddress_id'] = $this->Addresses->add_address($regaddress);
        $user['faktaddress_id'] = $this->Addresses->add_address($faktaddress);


        if (empty($user['user_id'])) {
            $user['stage_registration'] = '8';
            unset($user['user_id']);

            $last_personal_number = $this->users->last_personal_number();

            $user['personal_number'] = $last_personal_number + 1;
            $user['original'] = 1;

            if ($user['user_id'] = $this->users->add_user($user)) {

                $this->UserContactPreferred->delete($user['user_id']);

                if (!empty($user['sms_not']) && $user['sms_not'] == 1) {
                    $preferred =
                        [
                            'user_id' => $user['user_id'],
                            'contact_type_id' => 1
                        ];
                    $this->UserContactPreferred->add($preferred);
                }
                if (!empty($user['email_not']) && $user['email_not'] == 1) {
                    $preferred =
                        [
                            'user_id' => $user['user_id'],
                            'contact_type_id' => 2
                        ];
                    $this->UserContactPreferred->add($preferred);
                }
                if (!empty($user['massanger_not']) && $user['massanger_not'] == 1) {
                    $preferred =
                        [
                            'user_id' => $user['user_id'],
                            'contact_type_id' => 4
                        ];
                    $this->UserContactPreferred->add($preferred);

                    $preferred =
                        [
                            'user_id' => $user['user_id'],
                            'contact_type_id' => 3
                        ];
                    $this->UserContactPreferred->add($preferred);
                }
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
            $user['stage_registration'] = '8';

            $this->Contacts->delete($old_user->id);

            $contact =
                [
                    'user_id' => $old_user->id,
                    'type' => 'email',
                    'value' => $user['email']
                ];

            $this->Contacts->add($contact);

            if (isset($user['telegram_num'])) {
                $contact =
                    [
                        'user_id' => $old_user->id,
                        'type' => 'telegram',
                        'value' => $user['telegram_num']
                    ];

                $this->Contacts->add($contact);
            }

            if (isset($user['viber_num'])) {
                $contact =
                    [
                        'user_id' => $old_user->id,
                        'type' => 'viber',
                        'value' => $user['viber_num']
                    ];

                $this->Contacts->add($contact);
            }

            $this->UserContactPreferred->delete($user_id);

            if (!empty($user['sms_not']) && $user['sms_not'] == 1) {
                $preferred =
                    [
                        'user_id' => $user_id,
                        'contact_type_id' => 1
                    ];
                $this->UserContactPreferred->add($preferred);
            }
            if (!empty($user['sms_not']) && $user['email_not'] == 1) {
                $preferred =
                    [
                        'user_id' => $user_id,
                        'contact_type_id' => 2
                    ];
                $this->UserContactPreferred->add($preferred);
            }
            if (!empty($user['sms_not']) && $user['massanger_not'] == 1) {
                $preferred =
                    [
                        'user_id' => $user_id,
                        'contact_type_id' => 4
                    ];
                $this->UserContactPreferred->add($preferred);

                $preferred =
                    [
                        'user_id' => $user_id,
                        'contact_type_id' => 3
                    ];
                $this->UserContactPreferred->add($preferred);
            }

            $this->users->update_user($user_id, $user);

            $card_id = $this->request->post('card_id');

            if ($payout_type == 'bank' || $settlement_id == 3) {

                if (!empty($card_id)) {
                    $this->Cards->delete_card($card_id);
                    $card_id = null;
                }

                if (empty($requisite['id'])) {
                    unset($requisite['id']);
                    RequisitesORM::where('user_id', $user_id)->update(['default' => 0]);
                    $requisite['user_id'] = $user_id;
                    $requisite['id'] = $this->requisites->add_requisite($requisite);
                } else {
                    $this->requisites->update_requisite($requisite['id'], $requisite);
                }
            }

            if ($payout_type == 'card') {

                if (!empty($requisite['id'])) {
                    $this->requisites->delete_requisite($requisite['id']);
                    $requisite['id'] = null;
                }

                $card_name = $this->request->post('card_name');
                $pan = $this->request->post('pan');
                $expdate = $this->request->post('expdate');

                $card =
                    [
                        'name' => $card_name,
                        'user_id' => $user_id,
                        'base_card' => 1,
                        'pan' => $pan,
                        'expdate' => $expdate
                    ];

                if (!empty($card_id)) {
                    $this->cards->update_card($card_id, $card);
                } else {
                    $card_id = $this->cards->add_card($card);
                }
            }


            $probably_start_date = date('Y-m-d H:i:s', strtotime($this->request->post('start_date')));
            $probably_end_date = date('Y-m-d H:i:s', strtotime($this->request->post('end_date')));
            $branche_id = (int)$this->request->post('branch');
            $company_id = (int)$this->request->post('company');

            if ($payout_type == 'bank') {

                if (date('Y-m-d') >= date('Y-m-d', strtotime($probably_start_date))) {
                    $timeOfTransitionToNextBankingDay = date('H:i', strtotime($this->Settings->time_of_transition_to_the_next_banking_day));

                    if ($settlement_id == 3 && date('H:i') >= $timeOfTransitionToNextBankingDay)
                        $probably_start_date = date('Y-m-d', strtotime('+1 days'));

                    if ($settlement_id == 2) {
                        if (date('H:i') >= $timeOfTransitionToNextBankingDay)
                            $probably_start_date = date('Y-m-d', strtotime('+2 days'));
                        else
                            $probably_start_date = date('Y-m-d', strtotime('+1 days'));
                    }

                    $check_date = $this->WeekendCalendar->check_date($probably_start_date);

                    if (!empty($check_date)) {
                        for ($i = 0; $i <= 15; $i++) {

                            $check_date = $this->WeekendCalendar->check_date($probably_start_date);

                            if (empty($check_date)) {
                                if ($settlement_id == 2) {
                                    if (date('H:i') >= $timeOfTransitionToNextBankingDay)
                                        $probably_start_date = date('Y-m-d H:i:s', strtotime($probably_start_date . '+1 days'));
                                }
                                break;
                            } else {
                                $probably_start_date = date('Y-m-d H:i:s', strtotime($probably_start_date . '+1 days'));
                            }
                        }
                    }
                }
                $probably_end_date = $this->check_date($probably_start_date, $loan_type, $branche_id, $company_id);
            }

            $start_date = new DateTime(date('Y-m-d', strtotime($probably_start_date)));
            $end_date = new DateTime(date('Y-m-d', strtotime($probably_end_date)));
            $period = date_diff($start_date, $end_date)->days;


            $this->design->assign('period', $period);

            $order = array(
                'user_id' => $user_id,
                'amount' => $amount,
                'period' => $period,
                'card_id' => $card_id,
                'date' => date('Y-m-d H:i:s'),
                'manager_id' => $this->manager->id,
                'status' => ($draft == 1) ? 12 : 0,
                'offline' => 1,
                'charge' => $charge,
                'insure' => $insure,
                'loan_type' => (int)$loan_type,
                'probably_return_date' => date('Y-m-d', strtotime($probably_end_date)),
                'probably_start_date' => $probably_start_date,
                'probably_return_sum' => (int)preg_replace("/[^,.0-9]/", '', $this->request->post('probably_return_sum')),
                'group_id' => (int)$this->request->post('group'),
                'branche_id' => $branche_id,
                'company_id' => $company_id,
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

            if (empty($user['branche_id']) || $user['branche_id'] == 'none') {
                $branches = $this->Branches->get_branches(['company_id' => $user['company_id']]);

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
            $start_date = new DateTime(date('Y-m-d', strtotime($probably_start_date)));
            $paydate = new DateTime(date('Y-m-' . "$first_pay_day", strtotime($start_date->format('Y-m-d'))));
            $paydate->setDate($paydate->format('Y'), $paydate->format('m'), $first_pay_day);

            if ($start_date > $paydate)
                $paydate->add(new DateInterval('P1M'));

            $percent_per_month = (($order['percent'] / 100) * 365) / 12;
            $annoouitet_pay = $order['amount'] * ($percent_per_month / (1 - pow((1 + $percent_per_month), -$loan->max_period)));
            $annoouitet_pay = round($annoouitet_pay, '2');

            $iteration = 0;

            $count_days_this_month = date('t', strtotime($start_date->format('Y-m-d')));
            $paydate = $this->check_pay_date(new DateTime($paydate->format('Y-m-' . $first_pay_day)));

            if (date_diff($paydate, $start_date)->days <= $loan->free_period) {
                $plus_loan_percents = round(($order['percent'] / 100) * $order['amount'] * date_diff($paydate, $start_date)->days, 2);
                $sum_pay = $annoouitet_pay + $plus_loan_percents;
                $loan_percents_pay = round(($rest_sum * $percent_per_month) + $plus_loan_percents, 2, PHP_ROUND_HALF_DOWN);
                $body_pay = $sum_pay - $loan_percents_pay;
                $paydate->add(new DateInterval('P1M'));
                $iteration++;
            } elseif (date_diff($paydate, $start_date)->days >= $loan->min_period && date_diff($paydate, $start_date)->days < $count_days_this_month) {
                $minus_percents = ($order['percent'] / 100) * $order['amount'] * ($count_days_this_month - date_diff($paydate, $start_date)->days);
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
                $sum_pay = ($order['percent'] / 100) * $order['amount'] * date_diff($paydate, $start_date)->days;
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

                    if ($i == $period) {
                        $loan_body_pay = $rest_sum;
                        $loan_percents_pay = $annoouitet_pay - $loan_body_pay;
                        $rest_sum = 0.00;
                    } else {
                        $loan_percents_pay = round($rest_sum * $percent_per_month, 2, PHP_ROUND_HALF_DOWN);
                        $loan_body_pay = round($annoouitet_pay - $loan_percents_pay, 2);
                        $rest_sum = round($rest_sum - $loan_body_pay, 2);
                    }

                    if (isset($payment_schedule[$date->format('d.m.Y')])) {

                        $date = $this->add($date->format('d.m.Y'), 2);
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
                    'all_sum_pay' => 0.00,
                    'all_loan_percents_pay' => 0.00,
                    'all_loan_body_pay' => 0.00,
                    'all_comission_pay' => 0.00,
                    'all_rest_pay_sum' => 0.00
                ];

            $dates[0] = date('d.m.Y', strtotime($probably_start_date));
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

            $psk = round(((pow((1 + $xirr), (1 / 12)) - 1) * 12) * 100, 3);

            $month_pay = $amount * ((1 / $loan->max_period) + (($psk / 100) / 12));

            $all_sum_credits += $month_pay;

            if ($all_sum_credits != 0)
                $pdn = round(($all_sum_credits / $user['income']) * 100, 2);
            else
                $pdn = 0;

            $this->users->update_user($user_id, ['pdn' => $pdn]);

            $payment_schedule = json_encode($payment_schedule);

            $company = $this->Companies->get_company($order['company_id']);
            $group = $this->Groups->get_group($order['group_id']);

            $last_personal_number = $this->users->last_personal_number();
            $personal_number = $last_personal_number + 1;

            if (isset($user_id)) {
                $user = $this->users->get_user($user_id);

                if (empty($user->personal_number)) {
                    $this->users->update_user($user_id, ['personal_number' => $personal_number]);
                }

            }

            $order['uid'] = "$group->number$company->number " . $personal_number;

            if (($this->request->get('order_id'))) {
                $order_id = $this->request->get('order_id');
                $this->orders->update_order($order_id, $order);


                if ($draft == 1) {

                    response_json(['success' => 1, 'reason' => 'Заявка создана успешно', 'redirect' => $this->config->root_url . '/drafts/']);
                    exit;
                } else {

                    $schedules =
                        [
                            'user_id' => $user_id,
                            'order_id' => $order_id,
                            'created' => date('Y-m-d H:i:s'),
                            'type' => 'first',
                            'actual' => 1,
                            'schedule' => $payment_schedule,
                            'psk' => $psk,
                            'comment' => 'Первый график'
                        ];

                    $this->PaymentsSchedules->add($schedules);

                    $this->form_docs($order_id);

                    $loantype = $this->Loantypes->get_loantype($order['loan_type']);


                    ProjectContractNumberORM::updateOrCreate(
                        [
                            'orderId' => $order_id,
                            'userId' => $user_id
                        ],
                        [
                            'uid' => ProjectContractNumberORM::getNewNumber(
                                $group->number,
                                $company->number,
                                $loantype->number,
                                $personal_number,
                                $user_id,
                                $order_id,
                            )
                        ]
                    );

                    response_json(['success' => 1, 'reason' => 'Заявка создана успешно', 'redirect' => $this->config->root_url . '/offline_order/' . $order_id]);
                    exit;
                }
            } else {

                $order_id = $this->orders->add_order($order);

                if ($draft == 1) {
                    // запускаем бесплатные скоринги
                    $scoring_types = $this->scorings->get_types();
                    foreach ($scoring_types as $scoring_type) {
                        if ($scoring_type->name == 'fns' && empty($scoring_type->is_paid)) {
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
                    $user = $this->users->get_user($order['user_id']);

                    $schedules =
                        [
                            'user_id' => $user_id,
                            'order_id' => $order_id,
                            'created' => date('Y-m-d H:i:s'),
                            'type' => 'first',
                            'actual' => 1,
                            'schedule' => $payment_schedule,
                            'psk' => $psk,
                            'comment' => 'Первый график'
                        ];

                    $this->PaymentsSchedules->add($schedules);

                    // запускаем бесплатные скоринги
                    $scoring_types = $this->scorings->get_types();
                    foreach ($scoring_types as $scoring_type) {
                        if ($scoring_type->name == 'fns' && empty($scoring_type->is_paid)) {
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

                    $old_orders = $this->orders->get_orders(['user_id' => $user_id]);

                    $this->form_docs($order_id);

                    $loantype = $this->Loantypes->get_loantype($order['loan_type']);

                    ProjectContractNumberORM::updateOrCreate(
                        [
                            'orderId' => $order_id,
                            'userId' => $user_id
                        ],
                        [
                            'uid' => ProjectContractNumberORM::getNewNumber(
                                $group->number,
                                $company->number,
                                $loantype->number,
                                $personal_number,
                                $user_id,
                                $order_id
                            )
                        ]
                    );

                    response_json(['success' => 1, 'reason' => 'Заявка создана успешно', 'redirect' => $this->config->root_url . '/offline_order/' . $order_id]);
                }
            }
        }

        $this->design->assign('order', (object)$user);
    }

    private function action_edit_phone()
    {
        $phone = $this->request->post('phone');
        $user_id = $this->request->post('user_id');

        if (empty($user_id))
            $user_id = false;

        $phone_uniq = $this->users->get_phone_user($phone, $user_id);

        if (!empty($phone_uniq)) {
            echo json_encode(['error' => 'Такой номер уже зарегистрирован']);
            exit;
        }

        if (empty($phone)) {
            echo json_encode(['error' => 'Вы не ввели номер телефона']);
            exit;
        }

        UsersORM::where('id', $user_id)->update([
            'phone_mobile' => \App\Helpers\PhoneHelpers::format($phone, 'long_to_small'),
        ]);

        $code = random_int(1000, 9999);

        $template = $this->sms->get_template(4);
        $message = str_replace('$code', $code, $template->template);

        $response = $this->sms->send(
            $phone,
            $message
        );
        $this->db->query('
        INSERT INTO s_sms_messages
        SET phone = ?, code = ?, response = ?, ip = ?, created = ?
        ', $phone, $code, $response['resp'], $_SERVER['REMOTE_ADDR'] ?? '', date('Y-m-d H:i:s'));
        echo json_encode([
            'success' => 1,
            'code' => $code
        ]);
        exit;
    }

    private function action_edit_phone_with_code()
    {
        $phone = $this->request->post('phone');
        $code = $this->request->post('code');

        $this->db->query("
        SELECT code
        FROM s_sms_messages
        WHERE phone = ?
        AND code = ?
        ORDER BY id DESC
        LIMIT 1
        ", $phone, $code);

        $result = $this->db->result();

        if (empty($result)) {
            echo json_encode(['error' => 1]);
            exit;
        }

        UsersORM::where('phone_mobile', \App\Helpers\PhoneHelpers::format($phone, 'long_to_small'))->update([
            'phone_mobile_confirmed' =>  1,
        ]);

        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_edit_email()
    {
        $email = $this->request->post('email');
        $user_id = $this->request->post('user_id');

        $validate_email = preg_match('/^[A-Za-z0-9._-]+@[A-Z0-9_-]+.+[A-Z]/i', $email);

        if (empty($user_id))
            $user_id = false;

        $email_uniq = $this->users->get_phone_user($email, $user_id);

        if (!empty($email_uniq)) {
            echo json_encode(['error' => 'Такой email уже зарегистрирован']);
            exit;
        }

        if (empty($email)) {
            echo json_encode(['error' => 'Не введён email']);
            exit;
        }

        if ($validate_email == 0) {
            echo json_encode(['error' => 'Проверьте правильность заполнения поля Электронной почты ']);
            exit;
        }

        $code = random_int(1000, 9999);

        $result = $this->db->query('
        INSERT INTO s_email_messages
        SET email = ?, code = ?, created = ?
        ', $email, $code, date('Y-m-d H:i:s'));

        $mail = new PHPMailer(false);

        //Server settings
        $mail->isSMTP();                                            //Send using SMTP
        $mail->CharSet = 'UTF-8';
        $mail->Host = 'mail.nic.ru';                          //Set the SMTP server to send through
        $mail->SMTPAuth = true;                                   //Enable SMTP authentication
        $mail->Username = 'noreply@re-aktiv.ru';                  //SMTP username
        $mail->Password = 'HG!_@H#*&!^!HwJSDJ2Wsqgq';             //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable implicit TLS encryption
        $mail->Port = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('noreply@re-aktiv.ru');
        $mail->addAddress($email);     //Add a recipient

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'RuCred | Ваш проверочный код для смены почты';
        $mail->Body = '<h1>Сообщите код андеррайтеру РуКреда: </h1>' . "<h2>$code</h2>";

        $mail->send();

        UsersORM::where('id', $user_id)->update(['email' => $email]);

        echo json_encode(['success' => $code]);
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
        UsersORM::where('email', $email)->update(['email_confirmed' => 1]);
        echo json_encode(['success' => 1]);
        exit;
    }

    private function check_date($start_date = null, $loan_id = null, $branche_id = null, $company_id = null)
    {

        $get = $this->request->get('start_date');

        if (!empty($get)) {
            $start_date = $this->request->get('start_date');
            $loan_id = $this->request->get('loan_id');
            $branche_id = $this->request->get('branche_id');
            $company_id = $this->request->get('company_id');
        }

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

        if (!empty($get)) {
            echo json_encode(['date' => $end_date->format('d.m.Y')]);
            exit;
        } else {
            return $end_date->format('d.m.Y');
        }
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

    private function action_sum_to_pay()
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

        if ($start_date > $paydate || date_diff($paydate, $start_date)->days <= $loan->free_period)
            $paydate->add(new DateInterval('P1M'));

        if ($loan_id == 1) {
            $percent_per_month = (($percent / 100) * 360) / 12;
            $period = 1;

            $annoouitet_pay = $amount * ($percent_per_month / (1 - pow((1 + $percent_per_month), -$loan->max_period)));
            $annoouitet_pay = round($annoouitet_pay, '2');

            $iteration = 0;

            $count_days_this_month = date('t', strtotime($start_date->format('Y-m-d')));

            $paydate = $this->check_pay_date(new \DateTime($paydate->format('Y-m-' . $first_pay_day)));

            if (date_diff($paydate, $start_date)->days <= $loan->free_period) {
                $plus_loan_percents = round(($percent / 100) * $amount * date_diff($paydate, $start_date)->days, 2);
                $sum_pay = $annoouitet_pay + $plus_loan_percents;
                $loan_percents_pay = round(($rest_sum * $percent_per_month) + $plus_loan_percents, 2);
                $body_pay = $sum_pay - $loan_percents_pay;
                $paydate->add(new DateInterval('P1M'));
                $iteration++;
            } elseif (date_diff($paydate, $start_date)->days >= $loan->min_period && date_diff($paydate, $start_date)->days < $count_days_this_month) {
                $body_pay = $rest_sum;
                $loan_percents_pay = $amount * ($percent / 100) * date_diff($paydate, $start_date)->days;
                $sum_pay = $body_pay + $loan_percents_pay;
                $iteration++;
            } elseif (date_diff($paydate, $start_date)->days >= $count_days_this_month) {
                $body_pay = $rest_sum;
                $loan_percents_pay = $amount * ($percent / 100) * date_diff($paydate, $start_date)->days;
                $sum_pay = $body_pay + $loan_percents_pay;
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

            $period -= $iteration;

            if ($rest_sum !== 0) {
                for ($i = 1; $i <= $period; $i++) {
                    $paydate->setDate($paydate->format('Y'), $paydate->format('m'), $first_pay_day);
                    $date = $this->check_pay_date($paydate);

                    if (isset($payment_schedule[$date->format('d.m.Y')])) {
                        $date = $this->add_month($date->format('d.m.Y'), 2);
                        $paydate->setDate($date->format('Y'), $date->format('m'), $first_pay_day);
                        $date = $this->check_pay_date($paydate);
                    }

                    $loan_body_pay = $rest_sum;
                    $loan_percents_pay = $amount * ($percent / 100) * date_diff($start_date, $date)->days - $loan_percents_pay;
                    $annoouitet_pay = $loan_body_pay + $loan_percents_pay;
                    $rest_sum = 0.00;

                    $payment_schedule[$date->format('d.m.Y')] =
                        [
                            'pay_sum' => $annoouitet_pay,
                            'loan_percents_pay' => $loan_percents_pay,
                            'loan_body_pay' => $loan_body_pay,
                            'comission_pay' => 0.00,
                            'rest_pay' => $rest_sum
                        ];

                    $paydate->add(new \DateInterval('P1M'));
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
            $percent_per_month = (($percent / 100) * 365) / 12;
            $annoouitet_pay = $amount * ($percent_per_month / (1 - pow((1 + $percent_per_month), -$loan->max_period)));
            $annoouitet_pay = round($annoouitet_pay, '2');
            echo json_encode($annoouitet_pay);
            exit;
        }
    }

    public static function add_month($date_str, $months)
    {
        $date = new \DateTime($date_str);

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

    private function action_confirm_messengers()
    {
        $type = $this->request->post('type');
        $phone = $this->request->post('phone');
        $user_id = $this->request->post('user_id');
        $email = $this->request->post('email');
        $user_token = md5(time());
        $user_token = substr($user_token, 1, 10);

        if (empty($user_id)) {
            $user_id = $this->users->add_user(['created' => date('Y-m-d H:i:s')]);
        }

        switch ($type):
            case 'telegram':

                $template = $this->sms->get_template(5);
                $message = str_replace('$user_token', $user_token, $template->template);
                $resp = json_encode($this->sms->send($phone, $message));

                $user =
                    [
                        'user_id' => $user_id,
                        'token' => $user_token,
                        'is_manager' => 0
                    ];

                $this->TelegramUsers->add($user);

                $log =
                    [
                        'user_id' => $user_id,
                        'is_manager' => 0,
                        'type_id' => 1,
                        'resp' => $resp,
                        'text' => $message
                    ];

                $this->NotificationsLogs->add($log);
                UsersORM::where('id', $user_id)->update([
                    'telegram_num' => \App\Helpers\PhoneHelpers::format($phone, 'long_to_small'),
                ]);
                break;

            case 'viber':

                $mail = new PHPMailer(false);

                //Server settings
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host = 'mail.nic.ru';                          //Set the SMTP server to send through
                $mail->CharSet = 'UTF-8';
                $mail->SMTPAuth = true;                                   //Enable SMTP authentication
                $mail->Username = 'noreply@re-aktiv.ru';                  //SMTP username
                $mail->Password = 'HG!_@H#*&!^!HwJSDJ2Wsqgq';             //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable implicit TLS encryption
                $mail->Port = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                //Recipients
                $mail->setFrom('noreply@re-aktiv.ru');
                $mail->addAddress($email);     //Add a recipient

                //Content
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = 'RuCred | Ссылка для привязки Viber';
                $mail->Body = '<h1>' . $this->config->back_url . '/redirect_api?user_id=' . $user_token . '</h1>';

                $mail->send();

                $user =
                    [
                        'user_id' => $user_id,
                        'token' => $user_token,
                        'is_manager' => 0
                    ];
                UsersORM::where('id', $user_id)->update([
                    'viber_num' => \App\Helpers\PhoneHelpers::format($phone, 'long_to_small'),
                ]);
                $this->ViberUsers->add($user);
                break;
        endswitch;

        echo json_encode(['success' => 1, 'type' => $type, 'user_id' => $user_id]);
        exit;
    }

    public function action_get_user()
    {

        $user_id = $this->request->post('user_id');

        $user = $this->users->get_user($user_id);

        $requisites = $this->Requisites->getDefault($user_id);

        if (!empty($requisites)) {
            $user_fio = "$user->lastname $user->firstname $user->patronymic";
            $requisites_fio = $requisites->holder;

            if ($user_fio == $requisites_fio)
                $user->requisites = $requisites;
        }

        $passport_serial = explode(' ', $user->passport_serial);
        $user->passport_series = $passport_serial[0];
        $user->passport_number = $passport_serial[1];

        $regaddress = $this->Addresses->get_address($user->regaddress_id);
        $faktaddress = $this->Addresses->get_address($user->regaddress_id);

        $user->regaddress = $regaddress->adressfull;
        $user->faktaddress = $faktaddress->adressfull;

        $fio_spouse = explode(' ', $user->fio_spouse);
        $user->spouse_lastname = $fio_spouse[0];
        $user->spouse_firstname = $fio_spouse[1];
        $user->spouse_patronymic = $fio_spouse[2];

        $user->regaddress = $regaddress->adressfull;
        $user->faktaddress = $faktaddress->adressfull;

        $user->contacts = $this->Contacts->get_contacts($user->id);

        echo json_encode($user);
        exit;
    }

    private function action_delete_draft()
    {
        $orderId = $this->request->post('orderId');

        $this->orders->delete_order($orderId);

        exit;
    }

    private function form_docs($order_id)
    {
        $order = $this->orders->get_order($order_id);
        $order->payment_schedule = (array)$this->PaymentsSchedules->get(['order_id' => $order_id, 'actual' => 1]);

        $doc_types =
            [
                '04.05' => 'SOGLASIE_NA_OBR_PERS_DANNIH',
                '04.06' => 'SOGLASIE_RUKRED_RABOTODATEL',
                '03.03' => 'SOGLASIE_RABOTODATEL',
            ];

        if ($order->settlement_id == 2)
            $doc_types['04.05.1'] = 'SOGLASIE_MINB';
        else
            $doc_types['04.05.2'] = 'SOGLASIE_RDB';

        $doc_types['04.07'] = 'SOGLASIE_NA_KRED_OTCHET';
        $doc_types['04.03.02'] = 'INDIVIDUALNIE_USLOVIA';
        $doc_types['04.04'] = 'GRAFIK_OBSL_MKR';
        $doc_types['04.12'] = 'PERECHISLENIE_ZAEMN_SREDSTV';
        $doc_types['04.09'] = 'ZAYAVLENIE_NA_PERECHISL_CHASTI_ZP';
        $doc_types['04.10'] = 'OBSHIE_USLOVIYA';
        $doc_types['03.04'] = 'ZAYAVLENIE_ZP_V_SCHET_POGASHENIYA_MKR';

        foreach ($doc_types as $key => $type) {

            $this->documents->create_document(array(
                'user_id' => $order->user_id,
                'order_id' => $order->order_id,
                'stage_type' => 'reg-docs',
                'type' => $type,
                'params' => $order,
                'numeration' => (string)$key,
                'hash' => sha1(rand(11111, 99999))
            ));
        }
    }

    private function add($date_str, $months)
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
}
