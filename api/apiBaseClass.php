<?php

namespace Api;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class apiBaseClass extends \Core {

    public $checkToken  = false;

    public function json_response($res) {
      header("Content-type: application/json; charset=UTF-8");
      echo json_encode($res, JSON_PRETTY_PRINT);
      return;
    }

    public function get_jwt_token() {

        $headers = $this->getAuthorizationHeader();
        
        var_dump($headers);
        /* $key = 'example_key';
        $payload = [
            'iss' => 'http://example.org',
            'aud' => 'http://example.com',
            'iat' => time(), //дата выдачи токена
            'nbf' => time(), //дата активации токена
            'exp' => time() + 50, //время жизни токена
        ];
        $test = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vZXhhbXBsZS5vcmciLCJhdWQiOiJodHRwOi8vZXhhbXBsZS5jb20iLCJpYXQiOjE2NTA4OTMxNTEsIm5iZiI6MTY1MDg5MzE1MSwiZXhwIjoxNjUwODkzMjAxfQ.PA9fLGuCc8sN-woVpkCAKoyXAkU2T_9liyq8_1cjYo0';
//var_dump(time());


        $jwt = JWT::encode($payload, $key, 'HS256');
        //var_dump($jwt);

        //$decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        //var_dump($decoded);
        //JWT::$leeway = 5; // $leeway in seconds
        $decoded = JWT::decode($test, new Key($key, 'HS256'));
        var_dump($decoded); */

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