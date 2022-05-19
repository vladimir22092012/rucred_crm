<?php

namespace Api\routers;

use Api\apiBaseClass;
use Api\apiConstants;

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
            'next' => APIConstants::$URL_API . 'api/registration/stage/personal',
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
          'next' => APIConstants::$URL_API . 'api/registration/stage/passport',
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
          $errors[] = 'Отсутствует Серия и номер пасспорта';
        }

        if (array_key_exists('passport_date', $vars)) {
          $passport_date = $vars['passport_date'];
        } else {
          $errors[] = 'Отсутствует Дата выдачи пасспорта';
        }

        if (array_key_exists('passport_issued', $vars)) {
          $passport_issued = $vars['passport_issued'];
        } else {
          $errors[] = 'Отсутствует Кем выдан пасспорта';
        }

        if (array_key_exists('subdivision_code', $vars)) {
          $subdivision_code = $vars['subdivision_code'];
        } else {
          $errors[] = 'Отсутствует Код подразделения';
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
          'next' => APIConstants::$URL_API . 'api/registration/stage/address',
        ];

        $this->json_response($res);

      } catch (\Throwable $th) {

        $msg = 'Ошибка при регистрации нового пользователя(stage Passport)';
        $this->error_response($msg);

      }
    }

    public function stageAddress($vars) {
      try {

        $errors = [];

        if (array_key_exists('user_id', $vars)) {
          $user_id = $vars['user_id'];
        } else {
          $errors[] = 'Отсутствует id пользователя';
        }

        if (array_key_exists('faktregion', $vars)) {
          $faktregion = $vars['faktregion'];
        } else {
          $errors[] = 'Отсутствует Регион';
        }

        if (array_key_exists('faktcity', $vars)) {
          $faktcity = $vars['faktcity'];
        } else {
          $errors[] = 'Отсутствует Город';
        }

        if (array_key_exists('fakthousing', $vars)) {
          $fakthousing = $vars['fakthousing'];
        } else {
          $errors[] = 'Отсутствует Дом';
        }

        if (! empty($errors)) {
          foreach ($errors as $value) {
            $this->error_response($value);
          }
        }

        if (array_key_exists('clone_address', $vars)) {
          $clone_address = $vars['clone_address'];
        } else {
          $clone_address = false;
        }

        $faktdistrict = '';
        $faktdistrict_shorttype = '';
        $faktlocality = '';
        $faktlocality_shorttype = '';
        $faktstreet = '';
        $faktbuilding = '';
        $faktroom = '';
        $faktindex = '';
        $faktregion_shorttype = '';
        $faktcity_shorttype = '';
        $faktstreet_shorttype = '';
        $faktokato = '';
        $faktoktmo = '';

        if (array_key_exists('faktdistrict', $vars)) {
          $faktdistrict = $vars['faktdistrict'];
        }

        if (array_key_exists('faktdistrict_shorttype', $vars)) {
          $faktdistrict_shorttype = $vars['faktdistrict_shorttype'];
        }

        if (array_key_exists('faktlocality', $vars)) {
          $faktlocality = $vars['faktlocality'];
        }

        if (array_key_exists('faktlocality_shorttype', $vars)) {
          $faktlocality_shorttype = $vars['faktlocality_shorttype'];
        }

        if (array_key_exists('faktstreet', $vars)) {
          $faktstreet = $vars['faktstreet'];
        }

        if (array_key_exists('faktbuilding', $vars)) {
          $faktbuilding = $vars['faktbuilding'];
        }

        if (array_key_exists('faktroom', $vars)) {
          $faktroom = $vars['faktroom'];
        }

        if (array_key_exists('faktindex', $vars)) {
          $faktindex = $vars['faktindex'];
        }

        if (array_key_exists('faktregion_shorttype', $vars)) {
          $faktregion_shorttype = $vars['faktregion_shorttype'];
        }

        if (array_key_exists('faktcity_shorttype', $vars)) {
          $faktcity_shorttype = $vars['faktcity_shorttype'];
        }

        if (array_key_exists('faktstreet_shorttype', $vars)) {
          $faktstreet_shorttype = $vars['faktstreet_shorttype'];
        }

        if (array_key_exists('faktokato', $vars)) {
          $faktokato = $vars['faktokato'];
        }

        if (array_key_exists('faktoktmo', $vars)) {
          $faktoktmo = $vars['faktoktmo'];
        }

        if ($clone_address) {

          $regregion = $faktregion;
          $regcity = $faktcity;
          $regdistrict = $faktdistrict;
          $regdistrict_shorttype = $faktdistrict_shorttype;
          $reglocality = $faktlocality;
          $reglocality_shorttype = $faktlocality_shorttype;
          $regstreet = $faktstreet;
          $reghousing = $fakthousing;
          $regbuilding = $faktbuilding;
          $regroom = $faktroom;
          $regindex = $faktindex;
          $regregion_shorttype = $faktregion_shorttype;
          $regcity_shorttype = $faktcity_shorttype;
          $regstreet_shorttype = $faktstreet_shorttype;
          $regokato = $faktokato;
          $regoktmo = $faktoktmo;

        } else {
          
          if (array_key_exists('regregion', $vars)) {
            $regregion = $vars['regregion'];
          } else {
            $errors[] = 'Отсутствует Регион места регистрации';
          }
  
          if (array_key_exists('regcity', $vars)) {
            $regcity = $vars['regcity'];
          } else {
            $errors[] = 'Отсутствует Город места регистрации';
          }
  
          if (array_key_exists('reghousing', $vars)) {
            $reghousing = $vars['reghousing'];
          } else {
            $errors[] = 'Отсутствует Дом места регистрации';
          }

          $regdistrict = '';
          $regdistrict_shorttype = '';
          $reglocality = '';
          $reglocality_shorttype = '';
          $regstreet = '';
          $regbuilding = '';
          $regroom = '';
          $regindex = '';
          $regregion_shorttype = '';
          $regcity_shorttype = '';
          $regstreet_shorttype = '';
          $regokato = '';
          $regoktmo = '';

          if (array_key_exists('regdistrict', $vars)) {
            $regdistrict = $vars['regdistrict'];
          }
  
          if (array_key_exists('regdistrict_shorttype', $vars)) {
            $regdistrict_shorttype = $vars['regdistrict_shorttype'];
          }
  
          if (array_key_exists('reglocality', $vars)) {
            $reglocality = $vars['reglocality'];
          }
  
          if (array_key_exists('reglocality_shorttype', $vars)) {
            $reglocality_shorttype = $vars['reglocality_shorttype'];
          }
  
          if (array_key_exists('regstreet', $vars)) {
            $regstreet = $vars['regstreet'];
          }
  
          if (array_key_exists('regbuilding', $vars)) {
            $regbuilding = $vars['regbuilding'];
          }
  
          if (array_key_exists('regroom', $vars)) {
            $regroom = $vars['regroom'];
          }
  
          if (array_key_exists('regindex', $vars)) {
            $regindex = $vars['regindex'];
          }
  
          if (array_key_exists('regregion_shorttype', $vars)) {
            $regregion_shorttype = $vars['regregion_shorttype'];
          }
  
          if (array_key_exists('regcity_shorttype', $vars)) {
            $regcity_shorttype = $vars['regcity_shorttype'];
          }
  
          if (array_key_exists('regstreet_shorttype', $vars)) {
            $regstreet_shorttype = $vars['regstreet_shorttype'];
          }
  
          if (array_key_exists('regokato', $vars)) {
            $regokato = $vars['regokato'];
          }
  
          if (array_key_exists('regoktmo', $vars)) {
            $regoktmo = $vars['regoktmo'];
          }

        }

        if (! empty($errors)) {
          foreach ($errors as $value) {
            $this->error_response($value);
          }
        }

        $update = [
          'Faktregion' => $faktregion,
          'Faktcity' => $faktcity,
          'Faktdistrict' => $faktdistrict,
          'Faktdistrict_shorttype' => $faktdistrict_shorttype,
          'Faktlocality' => $faktlocality,
          'Faktlocality_shorttype' => $faktlocality_shorttype,
          'Faktstreet' => $faktstreet,
          'Fakthousing' => $fakthousing,
          'Faktbuilding' => $faktbuilding,
          'Faktroom' => $faktroom,
          'Faktindex' => $faktindex,
          'Faktregion_shorttype' => $faktregion_shorttype,
          'Faktcity_shorttype' => $faktcity_shorttype,
          'Faktstreet_shorttype' => $faktstreet_shorttype,

          'Regregion' => $regregion,
          'Regcity' => $regcity,
          'Regdistrict' => $regdistrict,
          'Regdistrict_shorttype' => $regdistrict_shorttype,
          'Reglocality' => $reglocality,
          'Reglocality_shorttype' => $reglocality_shorttype,
          'Regstreet' => $regstreet,
          'Reghousing' => $reghousing,
          'Regbuilding' => $regbuilding,
          'Regroom' => $regroom,
          'Regindex' => $regindex,
          'Regregion_shorttype' => $regregion_shorttype,
          'Regcity_shorttype' => $regcity_shorttype,
          'Regstreet_shorttype' => $regstreet_shorttype,

          'okato' => $regokato,
          'oktmo' => $regoktmo,

          'stage_address' => 1,
          'address_data_added_date' => date('Y-m-d H:i:s')
        ];

        $this->users->update_user($user_id, $update);

        $res = [
          'user_id' => $user_id,
          'next' => APIConstants::$URL_API . 'api/registration/stage/work',
        ];

        $this->json_response($res);

      } catch (\Throwable $th) {

        $msg = 'Ошибка при регистрации нового пользователя(stage Address)';
        $this->error_response($msg);
        
      }
    }

    public function stageWork($vars) {
      var_dump(3333);
    }

  }