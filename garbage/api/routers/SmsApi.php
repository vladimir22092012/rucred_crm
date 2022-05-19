<?php

namespace Api\routers;

use Api\apiBaseClass;

class SmsApi extends apiBaseClass {

    public function sendCode($vars) {
      try {

        if (array_key_exists('phone', $vars)) {
          $phone = $vars['phone'];
        } else {
          $msg = 'Введите номер телефона';
          $this->error_response($msg);
        }

        $rand_code = mt_rand(1000, 9999);

        $sms_message = array(
          'code' => $rand_code,
          'phone' => $phone,
          'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
          'created' => date('Y-m-d H:i:s'),                                
      );

      $send_response = $this->sms->send($phone, $rand_code);
      $sms_message['response'] = $send_response;

      $this->sms->add_message($sms_message);

      $res = ['result' => 'Код отправлен'];
      $this->json_response($res);

      } catch (\Throwable $th) {

        $msg = 'Ошибка при отправке смс';
        $this->error_response($msg);

      }
    }

  }