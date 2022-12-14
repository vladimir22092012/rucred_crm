<?php

error_reporting(-1);
ini_set('display_errors', 'On');
class ClientController extends Controller
{
    public function fetch()
    {
        if ($this->request->method('post')) {
            $id = $this->request->post('id', 'integer');
            $action = $this->request->post('action', 'string');

            switch ($action) :
                case 'contactdata':
                    $this->contactdata_action();
                    break;

                case 'services':
                    $this->action_services();
                    break;

                case 'personal':
                    $this->action_personal();
                    break;

                case 'passport':
                    $this->action_passport();
                    break;

                case 'reg_address':
                    $this->reg_address_action();
                    break;

                case 'fakt_address':
                    $this->fakt_address_action();
                    break;

                case 'contacts':
                    $this->contacts_action();
                    break;

                case 'work':
                    $this->work_action();
                    break;

                case 'work_address':
                    $this->work_address_action();
                    break;

                case 'socials':
                    $this->socials_action();
                    break;

                case 'images':
                    $this->action_images();
                    break;

                case 'blocked':
                    $this->action_blocked();
                    break;

                case 'edit_personal_number':
                    return $this->action_edit_personal_number();
                    break;

                case 'change_employer_info':
                    return $this->action_change_employer_info();
                    break;

                case 'get_companies':
                    return $this->action_get_companies();
                    break;

                case 'get_branches':
                    return $this->action_get_branches();
                    break;

                case 'delete_client':
                    $this->action_delete_client();
                    break;

                case 'blacklist':
                    $this->action_blacklist();
                    break;

                case 'sendOnecTrigger':
                    $this->actionSendOnecTrigger();
                    break;

                case 'sendYaDiskTrigger':
                    $this->actionSendYaDiskTrigger();
                    break;

            endswitch;
        } else {
            if (!($id = $this->request->get('id', 'integer'))) {
                return false;
            }

            if (!($client = $this->users->get_user($id))) {
                return false;
            }

            $in_blacklist = 0;

            $fio = "$client->lastname $client->firstname $client->patronymic";
            $phone = $client->phone_mobile;

            $blacklist_id = $this->blacklist->search($phone, $fio);

            if(!empty($blacklist_id))
                $in_blacklist = 1;

            $this->design->assign('in_blacklist', $in_blacklist);

            $client->regaddress = $this->addresses->get_address($client->regaddress_id);
            $client->faktaddress = $this->addresses->get_address($client->faktaddress_id);

            $user_close_orders = $this->orders->get_orders(array(
                'user_id' => $client->id,
                'type' => 'base',
                'status' => array(7)
            ));
            $client->have_crm_closed = !empty($user_close_orders);

            $managers_roles = $this->ManagerRoles->get();

            foreach ($managers_roles as $role) {
                if ($this->manager->role == $role->name)
                    $filter['role_id'] = $role->id;
            }

            $filter['user_id'] = $client->id;


            $documents = $this->documents->get_documents($filter);
            $sort_docs = [];

            if (!empty($documents)) {
                foreach ($documents as $document) {
                    $order = $this->orders->get_order($document->order_id);

                    if (empty($order)) {
                        unset($document);
                        continue;
                    }

                    $sort_docs[$document->order_id][$order->uid][] = $document;
                }
                $this->design->assign('sort_docs', $sort_docs);
            }

            $managers = array();
            foreach ($this->managers->get_managers() as $m) {
                $managers[$m->id] = $m;
            }
            $this->design->assign('managers', $managers);

            $files = $this->users->get_files(array('user_id' => $id));
            $this->design->assign('files', $files);

            $comments = $this->comments->get_comments(array('user_id' => $client->id));
            foreach ($comments as $comment) {
                if (!empty($comment->manager_id)) {
                    $comment->letter = mb_substr($managers[$comment->manager_id]->name, 0, 1);
                }
            }
            $this->design->assign('comments', $comments);


            $scorings = array();
            foreach ($this->scorings->get_scorings(array('user_id' => $client->id)) as $scoring) {
                if ($scoring->type == 'juicescore') {
                    $scoring->body = unserialize($scoring->body);
                }
                if ($scoring->type == 'scorista') {
                    $scoring->body = json_decode($scoring->body);
                }

                $scorings[$scoring->type] = $scoring;
            }
            $this->design->assign('scorings', $scorings);

            $changelogs = $this->changelogs->get_changelogs(array('user_id' => $client->id));
            foreach ($changelogs as $changelog) {
                $changelog->user = $client;
                if (!empty($changelog->manager_id) && !empty($managers[$changelog->manager_id])) {
                    $changelog->manager = $managers[$changelog->manager_id];
                }
            }
            $changelog_types = $this->changelogs->get_types();

            $this->design->assign('changelog_types', $changelog_types);
            $this->design->assign('changelogs', $changelogs);

            if ($client->orders = $this->orders->get_orders(array('user_id' => $client->id))) {
                foreach ($client->orders as $o) {
                    if ($o->contract_id) {
                        $o->contract = $this->contracts->get_contract($o->contract_id);
                    }
                }
            }

            $this->design->assign('client', $client);

            $order_statuses = $this->orders->get_statuses();
            $this->design->assign('order_statuses', $order_statuses);

            $cards = $this->cards->get_cards(array('user_id' => $client->id));
            $this->design->assign('cards', $cards);
        }

        $scoring_types = array();
        foreach ($this->scorings->get_types(array('active' => true)) as $type) {
            $scoring_types[$type->name] = $type;
        }
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($scoring_types);echo '</pre><hr />';
        $this->design->assign('scoring_types', $scoring_types);

        $groups = $this->Groups->get_groups();
        $companies = $this->Companies->get_companies(['group_id' => $client->group_id]);

        foreach ($groups as $group) {
            if ($client->group_id == $group->id)
                $group_name = $group->name;
        }

        if (!empty($client->company_id)) {

            $branches = $this->Branches->get_branches(['company_id' => $client->company_id]);
            $this->design->assign('branches', $branches);

            if (!empty($client->branche_id)) {
                foreach ($branches as $branch) {
                    if ($client->branche_id == $branch->id)
                        $branch_name = $branch->name;
                }
            }

            if (!empty($client->company_id)) {
                foreach ($companies as $company) {
                    if ($client->company_id == $company->id)
                        $company_name = $company->name;
                }
            }
        }

        if (!isset($group_name))
            $group_name = 'Отсутствует группа';

        if (!isset($branch_name))
            $branch_name = 'По умолчанию';

        if (!isset($company_name))
            $company_name = 'Отсутствует компания';

        $this->design->assign('groups', $groups);
        $this->design->assign('companies', $companies);
        $this->design->assign('branch_name', $branch_name);
        $this->design->assign('company_name', $company_name);
        $this->design->assign('group_name', $group_name);

        $contracts = $this->contracts->get_contracts(['user_id' => $client->id]);

        $balances = 0;

        if (!empty($contracts)) {
            foreach ($contracts as $contract) {
                $balances += $contract->overpay;
            }
        }

        $this->design->assign('balances', $balances);


        return $this->design->fetch('client.tpl');
    }


    private function action_services()
    {
        $user_id = $this->request->post('user_id', 'integer');

        $client = new StdClass();
        $client->service_sms = (int)$this->request->post('service_sms');
        $client->service_insurance = (int)$this->request->post('service_insurance');
        $client->service_reason = (int)$this->request->post('service_reason');

        $services_error = array();

        if (empty($services_error)) {
            $update = array(
                'service_sms' => $client->service_sms,
                'service_insurance' => $client->service_insurance,
                'service_reason' => $client->service_reason,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val) {
                if ($old_user->$key != $update[$key]) {
                    $old_values[$key] = $old_user->$key;
                }
            }

            $log_update = array();
            foreach ($update as $k => $u) {
                if (isset($old_values[$k])) {
                    $log_update[$k] = $u;
                }
            }

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'services',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'order_id' => 0,
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);
        }

        $this->design->assign('services_error', $services_error);

        $client->id = $user_id;

        $this->design->assign('client', $client);
    }

    private function contactdata_action()
    {
        $user_id = $this->request->post('user_id', 'integer');

        $client = new StdClass();

        $client->email = trim($this->request->post('email'));
        $client->birth = trim($this->request->post('birth'));
        $client->birth_place = trim($this->request->post('birth_place'));
        $client->passport_serial = trim($this->request->post('passport_serial'));
        $client->passport_date = trim($this->request->post('passport_date'));
        $client->subdivision_code = trim($this->request->post('subdivision_code'));
        $client->passport_issued = trim($this->request->post('passport_issued'));

        $client->social = trim($this->request->post('social'));

        $contactdata_error = array();

        if (empty($client->email)) {
            $personal_error[] = 'empty_email';
        }
        if (empty($client->birth)) {
            $personal_error[] = 'empty_birth';
        }
        if (empty($client->birth_place)) {
            $personal_error[] = 'empty_birth_place';
        }
        if (empty($client->passport_serial)) {
            $personal_error[] = 'empty_passport_serial';
        }
        if (empty($client->passport_date)) {
            $personal_error[] = 'empty_passport_date';
        }
        if (empty($client->subdivision_code)) {
            $personal_error[] = 'empty_subdivision_code';
        }
        if (empty($client->passport_issued)) {
            $personal_error[] = 'empty_passport_issued';
        }
        if (empty($client->social)) {
            $personal_error[] = 'empty_socials';
        }


        if (empty($contactdata_error)) {
            $update = array(
                'email' => $client->email,
                'birth' => $client->birth,
                'birth_place' => $client->birth_place,
                'passport_serial' => $client->passport_serial,
                'passport_date' => $client->passport_date,
                'subdivision_code' => $client->subdivision_code,
                'passport_issued' => $client->passport_issued,
                'social' => $client->social,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val) {
                if ($old_user->$key != $update[$key]) {
                    $old_values[$key] = $old_user->$key;
                }
            }

            $log_update = array();
            foreach ($update as $k => $u) {
                if (isset($old_values[$k])) {
                    $log_update[$k] = $u;
                }
            }

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'contactdata',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'order_id' => 0,
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);
        }

        $this->design->assign('contactdata_error', $contactdata_error);

        $client->id = $user_id;
        $this->design->assign('client', $client);
    }


    private function action_personal()
    {
        $user_id = $this->request->post('user_id', 'integer');

        $client = new StdClass();
        $client->lastname = trim($this->request->post('lastname'));
        $client->firstname = trim($this->request->post('firstname'));
        $client->patronymic = trim($this->request->post('patronymic'));
        $client->gender = trim($this->request->post('gender'));
        $client->birth = trim($this->request->post('birth'));
        $client->birth_place = trim($this->request->post('birth_place'));

        $personal_error = array();

        if (empty($client->lastname)) {
            $personal_error[] = 'empty_lastname';
        }
        if (empty($client->firstname)) {
            $personal_error[] = 'empty_firstname';
        }
        if (empty($client->patronymic)) {
            $personal_error[] = 'empty_patronymic';
        }
        if (empty($client->gender)) {
            $personal_error[] = 'empty_gender';
        }
        if (empty($client->birth)) {
            $personal_error[] = 'empty_birth';
        }
        if (empty($client->birth_place)) {
            $personal_error[] = 'empty_birth_place';
        }

        if (empty($personal_error)) {
            $update = array(
                'lastname' => $client->lastname,
                'firstname' => $client->firstname,
                'patronymic' => $client->patronymic,
                'gender' => $client->gender,
                'birth' => $client->birth,
                'birth_place' => $client->birth_place,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val) {
                if ($old_user->$key != $update[$key]) {
                    $old_values[$key] = $old_user->$key;
                }
            }

            $log_update = array();
            foreach ($update as $k => $u) {
                if (isset($old_values[$k])) {
                    $log_update[$k] = $u;
                }
            }

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'personal',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);
        }

        $this->design->assign('personal_error', $personal_error);

        $client->id = $user_id;
        $this->design->assign('client', $client);
    }

    private function action_passport()
    {
        $user_id = $this->request->post('user_id', 'integer');

        $client = new StdClass();
        $client->passport_serial = trim($this->request->post('passport_serial'));
        $client->passport_date = trim($this->request->post('passport_date'));
        $client->subdivision_code = trim($this->request->post('subdivision_code'));
        $client->passport_issued = trim($this->request->post('passport_issued'));

        $passport_error = array();

        if (empty($client->passport_serial)) {
            $passport_error[] = 'empty_passport_serial';
        }
        if (empty($client->passport_date)) {
            $passport_error[] = 'empty_passport_date';
        }
        if (empty($client->subdivision_code)) {
            $passport_error[] = 'empty_subdivision_code';
        }
        if (empty($client->passport_issued)) {
            $passport_error[] = 'empty_passport_issued';
        }

        if (empty($passport_error)) {
            $update = array(
                'passport_serial' => $client->passport_serial,
                'passport_date' => $client->passport_date,
                'subdivision_code' => $client->subdivision_code,
                'passport_issued' => $client->passport_issued
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val) {
                if ($old_user->$key != $update[$key]) {
                    $old_values[$key] = $old_user->$key;
                }
            }

            $log_update = array();
            foreach ($update as $k => $u) {
                if (isset($old_values[$k])) {
                    $log_update[$k] = $u;
                }
            }

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'passport',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);
        }

        $this->design->assign('passport_error', $passport_error);

        $client->id = $user_id;
        $this->design->assign('client', $client);
    }

    private function reg_address_action()
    {
        $user_id = $this->request->post('user_id', 'integer');

        $client = new StdClass();
        $client->Regregion = trim($this->request->post('Regregion'));
        $client->Regcity = trim($this->request->post('Regcity'));
        $client->Regstreet = trim($this->request->post('Regstreet'));
        $client->Reghousing = trim($this->request->post('Reghousing'));
        $client->Regbuilding = trim($this->request->post('Regbuilding'));
        $client->Regroom = trim($this->request->post('Regroom'));

        $regaddress_error = array();

        if (empty($client->Regregion)) {
            $regaddress_error[] = 'empty_regregion';
        }
        if (empty($client->Regcity)) {
            $regaddress_error[] = 'empty_regcity';
        }
        if (empty($client->Regstreet)) {
            $regaddress_error[] = 'empty_regstreet';
        }
        if (empty($client->Reghousing)) {
            $regaddress_error[] = 'empty_reghousing';
        }

        if (empty($regaddress_error)) {
            $update = array(
                'Regregion' => $client->Regregion,
                'Regcity' => $client->Regcity,
                'Regstreet' => $client->Regstreet,
                'Reghousing' => $client->Reghousing,
                'Regbuilding' => $client->Regbuilding,
                'Regroom' => $client->Regroom,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val) {
                if ($old_user->$key != $update[$key]) {
                    $old_values[$key] = $old_user->$key;
                }
            }

            $log_update = array();
            foreach ($update as $k => $u) {
                if (isset($old_values[$k])) {
                    $log_update[$k] = $u;
                }
            }

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'regaddress',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);
        }

        $this->design->assign('regaddress_error', $regaddress_error);

        $client->id = $user_id;
        $this->design->assign('client', $client);
    }

    private function fakt_address_action()
    {
        $user_id = $this->request->post('user_id', 'integer');

        $client = new StdClass();
        $client->Faktregion = trim($this->request->post('Faktregion'));
        $client->Faktcity = trim($this->request->post('Faktcity'));
        $client->Faktstreet = trim($this->request->post('Faktstreet'));
        $client->Fakthousing = trim($this->request->post('Fakthousing'));
        $client->Faktbuilding = trim($this->request->post('Faktbuilding'));
        $client->Faktroom = trim($this->request->post('Faktroom'));

        $faktaddress_error = array();

        if (empty($client->Faktregion)) {
            $faktaddress_error[] = 'empty_faktregion';
        }
        if (empty($client->Faktcity)) {
            $faktaddress_error[] = 'empty_faktcity';
        }
        if (empty($client->Faktstreet)) {
            $faktaddress_error[] = 'empty_faktstreet';
        }
        if (empty($client->Fakthousing)) {
            $faktaddress_error[] = 'empty_fakthousing';
        }

        if (empty($faktaddress_error)) {
            $update = array(
                'Faktregion' => $client->Faktregion,
                'Faktcity' => $client->Faktcity,
                'Faktstreet' => $client->Faktstreet,
                'Fakthousing' => $client->Fakthousing,
                'Faktbuilding' => $client->Faktbuilding,
                'Faktroom' => $client->Faktroom,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val) {
                if ($old_user->$key != $update[$key]) {
                    $old_values[$key] = $old_user->$key;
                }
            }

            $log_update = array();
            foreach ($update as $k => $u) {
                if (isset($old_values[$k])) {
                    $log_update[$k] = $u;
                }
            }

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'faktaddress',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);
        }

        $this->design->assign('faktaddress_error', $faktaddress_error);

        $client->id = $user_id;
        $this->design->assign('client', $client);
    }

    private function contacts_action()
    {
        $user_id = $this->request->post('user_id', 'integer');

        $client = new StdClass();
        $client->contact_person_name = trim($this->request->post('contact_person_name'));
        $client->contact_person_phone = trim($this->request->post('contact_person_phone'));
        $client->contact_person_relation = trim($this->request->post('contact_person_relation'));
        $client->contact_person2_name = trim($this->request->post('contact_person2_name'));
        $client->contact_person2_phone = trim($this->request->post('contact_person2_phone'));
        $client->contact_person2_relation = trim($this->request->post('contact_person2_relation'));

        $contacts_error = array();

        if (empty($client->contact_person_name)) {
            $contacts_error[] = 'empty_contact_person_name';
        }
        if (empty($client->contact_person_phone)) {
            $contacts_error[] = 'empty_contact_person_phone';
        }
        if (empty($client->contact_person_relation)) {
            $contacts_error[] = 'empty_contact_person_relation';
        }
        if (empty($client->contact_person2_name)) {
            $contacts_error[] = 'empty_contact_person2_name';
        }
        if (empty($client->contact_person2_phone)) {
            $contacts_error[] = 'empty_contact_person2_phone';
        }
        if (empty($client->contact_person2_relation)) {
            $contacts_error[] = 'empty_contact_person2_relation';
        }

        if (empty($contacts_error)) {
            $update = array(
                'contact_person_name' => $client->contact_person_name,
                'contact_person_phone' => $client->contact_person_phone,
                'contact_person_relation' => $client->contact_person_relation,
                'contact_person2_name' => $client->contact_person2_name,
                'contact_person2_phone' => $client->contact_person2_phone,
                'contact_person2_relation' => $client->contact_person2_relation,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val) {
                if ($old_user->$key != $update[$key]) {
                    $old_values[$key] = $old_user->$key;
                }
            }

            $log_update = array();
            foreach ($update as $k => $u) {
                if (isset($old_values[$k])) {
                    $log_update[$k] = $u;
                }
            }

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'contacts',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);
        }

        $this->design->assign('contacts_error', $contacts_error);

        $client->id = $user_id;
        $this->design->assign('client', $client);
    }

    private function work_action()
    {
        $user_id = $this->request->post('user_id', 'integer');

        $client = new StdClass();
        $client->workplace = trim($this->request->post('workplace'));
        $client->workaddress = trim($this->request->post('workaddress'));
        $client->workcomment = trim($this->request->post('workcomment'));
        $client->profession = trim($this->request->post('profession'));
        $client->workphone = trim($this->request->post('workphone'));
        $client->income = trim($this->request->post('income'));
        $client->expenses = trim($this->request->post('expenses'));
        $client->chief_name = trim($this->request->post('chief_name'));
        $client->chief_position = trim($this->request->post('chief_position'));
        $client->chief_phone = trim($this->request->post('chief_phone'));

        $work_error = array();

        if (empty($client->workplace)) {
            $work_error[] = 'empty_workplace';
        }
        if (empty($client->profession)) {
            $work_error[] = 'empty_profession';
        }
        if (empty($client->workphone)) {
            $work_error[] = 'empty_workphone';
        }
        if (empty($client->income)) {
            $work_error[] = 'empty_income';
        }
        if (empty($client->expenses)) {
            $work_error[] = 'empty_expenses';
        }
        if (empty($client->chief_name)) {
            $work_error[] = 'empty_chief_name';
        }
        if (empty($client->chief_phone)) {
            $work_error[] = 'empty_chief_phone';
        }
        if (empty($client->chief_phone)) {
            $work_error[] = 'empty_chief_phone';
        }

        if (empty($work_error)) {
            $update = array(
                'workplace' => $client->workplace,
                'workaddress' => $client->workaddress,
                'workcomment' => $client->workcomment,
                'profession' => $client->profession,
                'workphone' => $client->workphone,
                'income' => $client->income,
                'expenses' => $client->expenses,
                'chief_name' => $client->chief_name,
                'chief_position' => $client->chief_position,
                'chief_phone' => $client->chief_phone,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val) {
                if ($old_user->$key != $update[$key]) {
                    $old_values[$key] = $old_user->$key;
                }
            }

            $log_update = array();
            foreach ($update as $k => $u) {
                if (isset($old_values[$k])) {
                    $log_update[$k] = $u;
                }
            }

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'workdata',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);
        }

        $this->design->assign('work_error', $workdata_error);

        $client->id = $user_id;
        $this->design->assign('client', $client);
    }


    private function work_address_action()
    {
        $user_id = $this->request->post('user_id', 'integer');

        $client = new StdClass();
        $client->Workregion = trim($this->request->post('Workregion'));
        $client->Workcity = trim($this->request->post('Workcity'));
        $client->Workstreet = trim($this->request->post('Workstreet'));
        $client->Workhousing = trim($this->request->post('Workhousing'));
        $client->Workbuilding = trim($this->request->post('Workbuilding'));
        $client->Workroom = trim($this->request->post('Workroom'));

        $workaddress_error = array();

        if (empty($client->Workregion)) {
            $workaddress_error[] = 'empty_workregion';
        }
        if (empty($client->Workcity)) {
            $workaddress_error[] = 'empty_workcity';
        }
        if (empty($client->Workstreet)) {
            $workaddress_error[] = 'empty_workstreet';
        }
        if (empty($client->Workhousing)) {
            $workaddress_error[] = 'empty_workhousing';
        }

        if (empty($workaddress_error)) {
            $update = array(
                'Workregion' => $client->Workregion,
                'Workcity' => $client->Workcity,
                'Workstreet' => $client->Workstreet,
                'Workhousing' => $client->Workhousing,
                'Workbuilding' => $client->Workbuilding,
                'Workroom' => $client->Workroom,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val) {
                if ($old_user->$key != $update[$key]) {
                    $old_values[$key] = $old_user->$key;
                }
            }

            $log_update = array();
            foreach ($update as $k => $u) {
                if (isset($old_values[$k])) {
                    $log_update[$k] = $u;
                }
            }

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'workaddress',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);
        }

        $this->design->assign('workaddress_error', $workaddress_error);

        $client->id = $user_id;
        $this->design->assign('client', $client);
    }

    private function socials_action()
    {
        $user_id = $this->request->post('user_id', 'integer');

        $client = new StdClass();
        $client->social_fb = trim($this->request->post('social_fb'));
        $client->social_inst = trim($this->request->post('social_inst'));
        $client->social_vk = trim($this->request->post('social_vk'));
        $client->social_ok = trim($this->request->post('social_ok'));

        $socials_error = array();

        if (empty($socials_error)) {
            $update = array(
                'social_fb' => $client->social_fb,
                'social_inst' => $client->social_inst,
                'social_vk' => $client->social_vk,
                'social_ok' => $client->social_ok,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val) {
                if ($old_user->$key != $update[$key]) {
                    $old_values[$key] = $old_user->$key;
                }
            }

            $log_update = array();
            foreach ($update as $k => $u) {
                if (isset($old_values[$k])) {
                    $log_update[$k] = $u;
                }
            }

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'socials',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);
        }

        $this->design->assign('socials_error', $socials_error);

        $client->id = $user_id;
        $this->design->assign('client', $client);
    }


    private function action_images()
    {
        $user_id = $this->request->post('user_id', 'integer');

        $statuses = $this->request->post('status');
        foreach ($statuses as $file_id => $status) {
            $update = array(
                'status' => $status,
                'id' => $file_id
            );
            $old_files = $this->users->get_file($file_id);
            $old_values = array();
            foreach ($update as $key => $val) {
                $old_values[$key] = $old_files->$key;
            }
            if ($old_values['status'] != $update['status']) {
                $this->changelogs->add_changelog(array(
                    'manager_id' => $this->manager->id,
                    'created' => date('Y-m-d H:i:s'),
                    'type' => 'images',
                    'old_values' => serialize($old_values),
                    'new_values' => serialize($update),
                    'user_id' => $user_id,
                    'file_id' => $file_id,
                ));
            }

            $this->users->update_file($file_id, array('status' => $status));
        }

        $client = new StdClass();
        $client->id = $user_id;
        $this->design->assign('client', $client);

        $files = $this->users->get_files(array('user_id' => $user_id));
        $this->design->assign('files', $files);
    }

    private function action_blocked()
    {
        $user_id = $this->request->post('user_id', 'integer');

        $client = new StdClass();
        $client->blocked = (int)$this->request->post('blocked');

        $update = array(
            'blocked' => $client->blocked,
        );

        $old_user = $this->users->get_user($user_id);
        $old_values = array();
        foreach ($update as $key => $val) {
            if ($old_user->$key != $update[$key]) {
                $old_values[$key] = $old_user->$key;
            }
        }

        $log_update = array();
        foreach ($update as $k => $u) {
            if (isset($old_values[$k])) {
                $log_update[$k] = $u;
            }
        }

        $this->changelogs->add_changelog(array(
            'manager_id' => $this->manager->id,
            'created' => date('Y-m-d H:i:s'),
            'type' => 'services',
            'old_values' => serialize($old_values),
            'new_values' => serialize($log_update),
            'order_id' => 0,
            'user_id' => $user_id,
        ));

        $this->users->update_user($user_id, $update);

        $client->id = $user_id;

        $this->design->assign('client', $client);
    }

    private function action_edit_personal_number()
    {
        $user_id = (int)$this->request->post('user_id');
        $number = (int)$this->request->post('number');

        if (strlen($number) > 6) {
            echo json_encode(['error' => 'Номер не может быть более 6 символов']);
            exit;
        }

        $number = str_pad($number, 6, 0, STR_PAD_LEFT);

        $check = $this->users->check_busy_number($number);

        if ($check && $check != 0) {
            echo json_encode(['error' => 'Такой номер уже есть']);
            exit;
        } else {
            $query = $this->db->placehold("
            SELECT id, uid
            FROM s_orders
            WHERE user_id = $user_id
            ");

            $this->db->query($query);
            $orders = $this->db->results();

            foreach ($orders as $order) {
                $uid = explode(' ', $order->uid);
                $uid[1] = (string)$number;
                $uid = implode(' ', $uid);

                $this->orders->update_order($order->id, ['uid' => $uid]);
            }

            $this->users->update_user($user_id, ['personal_number' => $number]);
            echo json_encode(['success' => 1]);
            exit;
        }
    }

    private function action_change_employer_info()
    {
        $user_id = $this->request->post('user_id');
        $group_id = $this->request->post('group');
        $company_id = $this->request->post('company');
        $branch_id = $this->request->post('branch');

        if ($branch_id == 'none')
            $branch_id = 0;

        $user =
            [
                'group_id' => $group_id,
                'company_id' => $company_id,
                'branche_id' => $branch_id
            ];

        $this->users->update_user($user_id, $user);
        exit;
    }

    private function action_get_companies()
    {
        $group_id = $this->request->post('group_id');

        $companies = $this->Companies->get_companies(['group_id' => $group_id, 'offline_blocked' => 0]);

        echo json_encode(['companies' => $companies]);
        exit;
    }

    private function action_get_branches()
    {
        $company_id = $this->request->post('company_id');

        $branches = $this->Branches->get_branches(['company_id' => $company_id]);

        echo json_encode(['branches' => $branches]);
        exit;
    }

    private function action_delete_client()
    {
        $user_id = $this->request->post('user_id');

        $check_orders = $this->orders->get_orders(['user_id' => $user_id]);

        if (!empty($check_orders)) {
            echo json_encode(['error' => 'Клиент был отправлен в 1с']);
        } else {
            echo json_encode(['success' => 'Пользователь успешно удален']);
        }
        exit;
    }

    private function action_blacklist()
    {
        $user_id = $this->request->post('user_id');
        $user = $this->users->get_user($user_id);

        $fio = "$user->lastname $user->firstname $user->patronymic";
        $phone = $user->phone_mobile;

        $blacklist_id = $this->blacklist->search($phone, $fio);

        if(!empty($blacklist_id))
            $this->blacklist->delete_person($blacklist_id);
        else{
            $person =
                [
                    'fio' => $fio,
                    'phone' => $phone
                ];

            $this->blacklist->add_person($person);
        }

        exit;
    }

    private function actionSendOnecTrigger()
    {
        $userId = $this->request->post('userId');
        $value = $this->request->post('value');

        UsersORM::where('id', $userId)->update(['canSendOnec' => $value]);
        exit;
    }

    private function actionSendYaDiskTrigger()
    {
        $userId = $this->request->post('userId');
        $value = $this->request->post('value');

        UsersORM::where('id', $userId)->update(['canSendYaDisk' => $value]);
        exit;
    }
}
