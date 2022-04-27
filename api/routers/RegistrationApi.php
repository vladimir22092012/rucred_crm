<?php

namespace Api\routers;

use Api\apiBaseClass;

class RegistrationApi extends apiBaseClass {

    public function stageMain($vars) {
      try {

        if (array_key_exists('phone', $vars)) {
          $phone = $vars['phone'];
          $phone = $this->sms->clear_phone($phone);

          if ($this->users->get_phone_user($phone)) {
            $msg = 'Клиент с номером телефона '.$phone.' уже зарегистрирован';
            $this->error_response($msg);
          }
        } else {
          $msg = 'Введите номер телефона';
          $this->error_response($msg);
        }

        if (array_key_exists('code', $vars)) {
          $code = $vars['code'];
          $db_code = $this->sms->get_code($phone);
          if ($db_code != $code) {
            $msg = 'Код из СМС не совпадает';
            $this->error_response($msg);
          }
        } else {
          $msg = 'Введите код';
          $this->error_response($msg);
        }

        $amount = $vars['amount'];
        $period = $vars['period'];
        $service_insurance = $vars['service_insurance'];
        $service_reason = $vars['service_reason'];

        $user = [
            'first_loan_amount' => $amount,
            'first_loan_period' => $period,
            'phone_mobile' => $phone,
            'sms' => $code,
            'service_reason' => $service_reason,
            'service_insurance' => $service_insurance,
            'reg_ip' => $_SERVER['REMOTE_ADDR'],
            'last_ip' => $_SERVER['REMOTE_ADDR'],
            'enabled' => 1,
            'created' => date('Y-m-d H:i:s'),
        ];

        $user_id = $this->users->add_user($user);

        $res = [
            'user_id' => $user_id,
            'next' => '/api/registration/stage/personal',
        ];

        $this->json_response($res);

      } catch (\Throwable $th) {

        $msg = 'Ошибка при регистрации нового пользователя(stage Main)';
        $this->error_response($msg);

      }
    }

    public function stagePersonal($vars) {
      try {
        $errors = [];

        if (array_key_exists('user_id', $vars)) {
          $user_id = $vars['user_id'];
        } else {
          $errors[] = 'Отсутствует id пользователя';
        }

        if (array_key_exists('lastname', $vars)) {
          $lastname = $vars['lastname'];
        } else {
          $errors[] = 'Введите Фамилию';
        }

        if (array_key_exists('firstname', $vars)) {
          $firstname = $vars['firstname'];
        } else {
          $errors[] = 'Введите Имя';
        }

        if (array_key_exists('patronymic', $vars)) {
          $patronymic = $vars['patronymic'];
        } else {
          $errors[] = 'Введите Отчество';
        }

        if (array_key_exists('email', $vars)) {
          $email = $vars['email'];
        } else {
          $errors[] = 'Введите Емайл';
        }

        if (array_key_exists('gender', $vars)) {
          $gender = $vars['gender'];
        } else {
          $errors[] = 'Укажите ваш пол';
        }

        if (array_key_exists('birth', $vars)) {
          $birth = $vars['birth'];
        } else {
          $errors[] = 'Введите Дату рождения';
        }

        if (array_key_exists('birth_place', $vars)) {
          $birth_place = $vars['birth_place'];
        } else {
          $errors[] = 'Введите Место рождения';
        }

        $social = '';
        if (array_key_exists('social', $vars)) {
          $social = $vars['social'];
        }

        if (! empty($errors)) {
          foreach ($errors as $value) {
            $this->error_response($value);
          }
        }

        $update = [
            'lastname' => $lastname,
            'firstname' => $firstname,
            'patronymic' => $patronymic,
            'email' => $email,
            'gender' => $gender,
            'birth' => $birth,
            'birth_place' => $birth_place,
            'social' => $social,
            'stage_personal' => 1,
            'stage_personal_date' => date('Y-m-d H:i:s'),
        ];

        $this->users->update_user($user_id, $update);

        $res = [
          'user_id' => $user_id,
          'next' => '/api/registration/stage/passport',
        ];

      $this->json_response($res);

      } catch (\Throwable $th) {

        $msg = 'Ошибка при регистрации нового пользователя(stage Personal)';
        $this->error_response($msg);

      }
    }

    public function stagePassport($vars) {
      try {

        $errors = [];

        if (array_key_exists('user_id', $vars)) {
          $user_id = $vars['user_id'];
        } else {
          $errors[] = 'Отсутствует id пользователя';
        }

        if (array_key_exists('passport_serial', $vars)) {
          $passport_serial = $vars['passport_serial'];
        } else {
          $errors[] = 'Отсутствует id пользователя';
        }

        if (array_key_exists('passport_date', $vars)) {
          $passport_date = $vars['passport_date'];
        } else {
          $errors[] = 'Отсутствует id пользователя';
        }

        if (array_key_exists('passport_issued', $vars)) {
          $passport_issued = $vars['passport_issued'];
        } else {
          $errors[] = 'Отсутствует id пользователя';
        }

        if (array_key_exists('subdivision_code', $vars)) {
          $subdivision_code = $vars['subdivision_code'];
        } else {
          $errors[] = 'Отсутствует id пользователя';
        }

        $snils = '';
        if (array_key_exists('snils', $vars)) {
          $snils = $vars['snils'];
        }

        if (! empty($errors)) {
          foreach ($errors as $value) {
            $this->error_response($value);
          }
        }

        $user = $this->users->get_user($user_id);
        // проверяем по базе паспортные данные
        if ($found_id = $this->users->find_clone($passport_serial, $user->lastname, $user->firstname, $user->patronymic, $user->birth))
        {
            if ($found_id != $user_id) {
              $msg = 'Данный пасспорт уже использовался у другого пользователя';
              $this->error_response($msg);
            }
        }

        $update = [
            'passport_serial' => $passport_serial,
            'passport_date' => $passport_date,
            'passport_issued' => $passport_issued,
            'subdivision_code' => $subdivision_code,
            'snils' => $snils,
            'stage_passport' => 1,
            'passport_date_added_date' => date('Y-m-d H:i:s'),
        ];

        $this->users->update_user($user_id, $update);

        $res = [
          'user_id' => $user_id,
          'next' => '/api/registration/stage/address',
        ];

        $this->json_response($res);

      } catch (\Throwable $th) {

        $msg = 'Ошибка при регистрации нового пользователя(stage Passport)';
        $this->error_response($msg);

      }
    }

    public function stageAddress($vars) {
      var_dump(2222);
    }

    public function stageWork($vars) {
      var_dump(3333);
    }

  }