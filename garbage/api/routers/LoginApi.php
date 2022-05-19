<?php

namespace Api\routers;

use Api\apiBaseClass;
use Api\apiConstants;
use Firebase\JWT\JWT;

class LoginApi extends apiBaseClass {

  public $userId;
  public $salt = '59e6b15488e86a2b3c811c5479d6e182';

    public function run($vars) {
      try {

        if (array_key_exists('login', $vars)) {
          $login = $vars['login'];
        } else {
          $msg = 'Введите логин';
          $this->error_response($msg);
        }

        if (array_key_exists('password', $vars)) {
          $password = $vars['password'];
        } else {
          $msg = 'Введите пароль';
          $this->error_response($msg);
        }

        $check = $this->checkUser($login, $password);
        if ($check['error']) {
          $this->error_response($check['msg']);
        }
        
        $token = $this->makeToken();
        $res = ['token' => $token];

        $this->json_response($res);

      } catch (\Throwable $th) {
        $msg = 'Ошибка Авторизации';
        $this->error_response($msg);
      }
    }

    public function makeToken() {
        $key = APIConstants::$KEY;
        $payload = [
            'iss' => APIConstants::$ISS,
            'iat' => time(), //дата выдачи токена
            'nbf' => time(), //дата активации токена
            'exp' => time() + APIConstants::$TIME_LIFE, //дата, после которой токен не действителен
            'user_id' => $this->userId
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');

        return $jwt;
    }
    
    public function checkUser($login, $password) {
      $result = [];
      $result['error'] = true;

      //Получаем пользователя
      $user = $this->users->get_user_by_phone($login);

      if (is_null($user)) {
        $result['msg'] = 'Пользователь с таким телефоном не найден';
        return $result;
      }

      $encpassword = $this->hash_password($password);

      if ($user->password != $encpassword) {
        $result['msg'] = 'Пароль не совпадает';
        return $result;
      }

      $this->userId = $user->id;

      $result['error'] = false;
      return $result;
    }

    public function hash_password($password) {
      return md5($this->salt.$password.sha1($password));
    }
  }