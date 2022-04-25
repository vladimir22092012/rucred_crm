<?php

namespace Api;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class apiBaseClass extends \Core {

    public $checkToken  = false;
    public $tokenJWT;
    public $userIdFromToken;

    public function __construct()
    {
    	parent::__construct();
        $this->get_jwt_token();
        
    }

    public function json_response($res) {
      header("Content-type: application/json; charset=UTF-8");
      echo json_encode($res, JSON_PRETTY_PRINT);
      return;
    }

    public function get_jwt_token() {
        if ($this->checkToken) {
            $this->tokenJWT =  $this->getBearerToken();
            $this->check_token();
        }

        $key = 'example_key';
        $payload = [
            'iss' => 'http://example.org',
            'iat' => time(), //дата выдачи токена
            'nbf' => time(), //дата активации токена
            'exp' => time() + 50, //время жизни токена
            'user_id' => 123
        ];
        $test = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vZXhhbXBsZS5vcmciLCJpYXQiOjE2NTA4OTkzNjgsIm5iZiI6MTY1MDg5OTM2OCwiZXhwIjoxNjUwODk5NDE4fQ.pjjoOFb5KpEjZ1PZ4rUJm59Dp9sjrL2GyLHDxi30DG0';
//var_dump(time());


        //$jwt = JWT::encode($payload, $key, 'HS256');
        //var_dump($jwt);

        //$decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        //var_dump($decoded);
        //JWT::$leeway = 5; // $leeway in seconds
        //$decoded = JWT::decode($test, new Key($key, 'HS256'));
        //var_dump($decoded);

    }

    public function check_token() {
        try {
            $decoded = JWT::decode($this->tokenJWT, new Key(APIConstants::$KEY, 'HS256'));
            $this->userIdFromToken = $decoded['user_id'];
        } catch (\Throwable $th) {
            $msg = 'Ошибка JWT токена';
            $this->error_response($msg);
        }
      }

    public function error_response($msg) {
        header("Content-type: application/json; charset=UTF-8");
        $res = [
            'error' => $msg
        ];
        echo json_encode($res, JSON_PRETTY_PRINT);
        return;
    }

    public function getAuthorizationHeader() {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    public function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

  }