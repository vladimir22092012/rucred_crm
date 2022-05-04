<?php

namespace Api\routers;

use Api\apiBaseClass;
use Api\apiConstants;

class AccountApi extends apiBaseClass {

  public $checkToken  = true;
  private $orderStatuses = [
      0 => 'Новая',
      1 => 'Р.Принята',
      2 => 'В обработке',
      3 => 'Р.Подтверждена',
      4 => 'Одобрена',
      5 => 'Принята',
      6 => 'Одобрена',
      7 => 'Отказ',
      8 => 'Подписан',
      9 => 'Выдан',
      10 => 'Отказ клиента',
      11 => 'Не удалось выдать',
      12 => 'Черновик'
  ];

    public function getProfile($vars) {
      try {

        $userId = $this->userIdFromToken;
        $user = $this->users->get_user($userId);
        
        $res = [
            'personal' => [
                'lastname'    => $user->lastname,
                'firstname'   => $user->firstname,
                'patronymic'  => $user->patronymic
            ],
            'contact' => [
              'phone'         => $user->phone_mobile,
              "email"         => $user->email,
              "titanid"       => 123456,
              "whatsapp"      => $user->whatsapp_num,
              "viber"         => $user->viber_num,
              "telegram"      => $user->telegram_num,
              "facebook"      => "https://facebook.com/123",
              "odnoklassniki" => "https://ok.com/123",
              "vk"            => "https://vk.com/123",
            ],
            'work' => [
                'workplace'   => $user->workplace,
                'workaddress' => $user->workaddress,
                'inn'         => 6315655854,
                'ogrn'        => 1146315000845,
                'kpp'         => 631501325,
            ],
        ];

        $this->json_response($res);

      } catch (\Throwable $th) {

        $msg = 'Ошибка пполучения профиля пользователя';
        $this->error_response($msg);

      }
    }

    public function getOrders($vars) {
      try {

        $userId = $this->userIdFromToken;
        $orders = $this->orders->get_orders(['user_id' => $userId]);

        $res = [];

        foreach ($orders as $key => $order) {
          $res['orders'][$key]['date'] = $order->date;
          $res['orders'][$key]['status'] = $this->orderStatuses[$order->status];
        }

        $this->json_response($res);

      } catch (\Throwable $th) {

        $msg = 'Ошибка получения списка ордеров';
        $this->error_response($msg);

      }
    }

    public function getDocuments($vars) {
      try {

        $userId = $this->userIdFromToken;
        $docs = $this->documents->get_documents(['user_id' => $userId, 'client_visible' => 1]);

        $res = [];

        foreach ($docs as $key => $doc) {
          $res['documents'][$key]['name'] = $doc->name;
          $res['documents'][$key]['link'] = APIConstants::$URL_SITE . 'document/' .$doc->user_id . '/' . $doc->id;
        }

        $this->json_response($res);

      } catch (\Throwable $th) {

        $msg = 'Ошибка получения списка документов';
        $this->error_response($msg);

      }
    }

    public function getFiles($vars) {
      try {

        $userId = $this->userIdFromToken;
        $userId = 181780;
        $files = $this->users->get_files(['user_id' => $userId]);

        $res = [];

        foreach ($files as $key => $file) {
          $res['files'][$key]['type'] = $file->type;
          $res['files'][$key]['link'] = APIConstants::$URL_SITE . 'files/users/' . $file->name;
        }

        $this->json_response($res);

      } catch (\Throwable $th) {

        $msg = 'Ошибка получения списка фотографий';
        $this->error_response($msg);

      }
    }

    public function addFiles($vars) {
      try {

        $userId = $this->userIdFromToken;
        $userId = 181780;
        $files = $this->users->get_files(['user_id' => $userId]);

        $res = [];

        foreach ($files as $key => $file) {
          $res['files'][$key]['type'] = $file->name;
          $res['files'][$key]['link'] = APIConstants::$URL_SITE . 'files/users/' . $file->name;
        }

        $this->json_response($res);

      } catch (\Throwable $th) {

        $msg = 'Ошибка при добавлении фотографии';
        $this->error_response($msg);

      }
    }

  }