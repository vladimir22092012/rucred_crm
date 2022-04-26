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

        $msg = 'Ошибка при регистрации нового пользователя';
        $this->error_response($msg);

      }
    }

    public function stagePersonal($vars) {
      var_dump(2222);
    }

  }