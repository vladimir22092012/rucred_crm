<?php

namespace Api\routers;

use Api\apiBaseClass;

class Test extends apiBaseClass {

    public function get($vars) {
      var_dump(1111);
    }

    public function test() {
        $user = $this->users->get_user(181780);

        //$this->get_jwt_token();
        //var_dump($this->checkToken);
        $res = [
            'method' => 'GET',
            'id' => $user,
            'good' => 'phone',
            'price' => 10000
        ];
dd($res);
        //$this->json_response($res);
        // Выводим ответ клиенту
        /* echo json_encode($res, JSON_PRETTY_PRINT);
        return; */
    }

  }