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
  private $max_file_size = 5242880;

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
        $files = $this->users->get_files(['user_id' => $userId]);

        $res = [];

        foreach ($files as $key => $file) {
          $res['files'][$key]['type'] = $file->type;
          $res['files'][$key]['link'] = 'files/users/' . $file->name;
        }

        $this->json_response($res);

      } catch (\Throwable $th) {

        $msg = 'Ошибка получения списка фотографий';
        $this->error_response($msg);

      }
    }

    public function addFile($vars) {
      try {

        $files = $_FILES;
        $userId = $this->userIdFromToken;

        if (count($files) < 1) {
          $msg = 'Не выбрано изображение для загрузки';
          $this->error_response($msg);
        }

        if (array_key_exists('type', $vars)) {
          $type =  $vars['type'];
        } else {
          $msg = 'Не выбран тип изображения';
          $this->error_response($msg);
        }

        $file = $files['file'];

        if ($this->max_file_size < $file['size']) {
          $msg = 'Превышен допустимый размер файла';
          $this->error_response($msg);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = md5(microtime() . rand()) . '.' . $ext;

        if (move_uploaded_file($file['tmp_name'], $this->config->root_dir . $this->config->user_files_dir . $new_filename)) {
          $file_id = $this->users->add_file(array(
            'user_id' => $userId,
            'name' => $new_filename,
            'type' => $type,
            'status' => 0
          ));
        } else {
          $msg = 'Ошибка при сохранении фотографии';
          $this->error_response($msg);
        }

        $res = [
          'result' => 'Изображение успешно загружено',
          'link'   => '/files/users/' . $new_filename
        ];
        $this->json_response($res);

      } catch (\Throwable $th) {

        $msg = 'Ошибка при добавлении фотографии';
        $this->error_response($msg);

      }
    }

    public function getRequesits($vars) {

      try {
        //todo: получение данных из бд
        $res = [
          'cards' => [
                [
                  'base_card' => 1,
                  'pan' => '427432******3562',
                  'expdate' => '09/2024'
                ],
                [
                  'base_card' => 0,
                  'pan' => '557432******3523',
                  'expdate' => '05/2029'
                ]
          ],
          'accounts'   => [
                [
                  'pc' => '40701810988000000275',
                  'akb' => '«ФОРА-БАНК» (АО)',
                  'bik' => '044525341',
                  'kc' => '30101810300000000341 в ГУ Банка России по ЦФО',
                  'holder' => 'Иванов Иван Иванович'
                ],
                [
                  'pc' => '50701810988000000275',
                  'akb' => '«БИБА-БАНК» (АО)',
                  'bik' => '055525341',
                  'kc' => '20101810300000000342 в ГУ Банка России по ЦФО',
                  'holder' => 'Иванов Иван Иванович'
                ]

          ]
        ];
        
        $this->json_response($res);

      } catch (\Throwable $th) {
        
        $msg = 'Ошибка при получении реквизитов';
        $this->error_response($msg);

      }
    }

    public function addCardRequesits($vars) {
      try {

        //todo: сохранение в бд
        if (array_key_exists('pan', $vars)) {
          $pan =  $vars['pan'];
        } else {
          $msg = 'Отсутствует номер карты';
          $this->error_response($msg);
        }

        if (array_key_exists('expdate', $vars)) {
          $expdate =  $vars['expdate'];
        } else {
          $msg = 'Отсутствует срок действия карты';
          $this->error_response($msg);
        }

        $res = [
          'result' => 'Карта успешно добавлена'
        ];
        $this->json_response($res);

      } catch (\Throwable $th) {

        $msg = 'Ошибка при добавлении карты';
        $this->error_response($msg);

      }
    }

    public function addAccountRequesits($vars) {
      try {
        //todo: сохранение в бд

        $res = [
          'result' => 'Счет успешно добавлен'
        ];
        $this->json_response($res);

      } catch (\Throwable $th) {

        $msg = 'Ошибка при добавлении счета';
        $this->error_response($msg);

      }
    }

    public function getLoans($vars) {
      try {
        
        $res = [
          [
            'id' => 100995,
            'amount' => 10000,
            'date_order' => '10.03.2022 | 11:44',
            'date_return' => '12.05.2022 | 21:27',
            'status' => 'Активен',
            'payments' => [
                  [
                    'amount' => 500,
                    'date' => '11.03.2022 | 12:44',
                    'status' => 'Успешно'
                  ],
                  [
                    'amount' => 300,
                    'date' => '12.03.2022 | 12:47',
                    'status' => 'Ожидается'
                  ],
                  [
                    'amount' => 1000,
                    'date' => '13.03.2022 | 12:45',
                    'status' => 'Просрочен'
                  ],

            ]
          ],
          [
            'id' => 100999,
            'amount' => 11000,
            'date_order' => '10.03.2022 | 11:44',
            'date_return' => '12.05.2022 | 21:27',
            'status' => 'Закрыт',
            'payments' => [
                  [
                    'amount' => 500,
                    'date' => '11.03.2022 | 12:44',
                    'status' => 'Успешно'
                  ],
                  [
                    'amount' => 300,
                    'date' => '12.03.2022 | 12:47',
                    'status' => 'Ожидается'
                  ],
                  [
                    'amount' => 1000,
                    'date' => '13.03.2022 | 12:45',
                    'status' => 'Просрочен'
                  ],

            ]
          ]
        ];

        $this->json_response($res);

      } catch (\Throwable $th) {

        $msg = 'Ошибка при получении истории займов';
        $this->error_response($msg);

      }
    }
    
    public function getCurrentLoan($vars) {
      try {

        $res = [
          'id' => 100995,
          'amount' => 10000,
          'loan_body_summ' => 10000,
          'loan_percents_summ' => 3800,
          'allready_paid' => 500,
          'date_order' => '10.03.2022 | 11:44',
          'date_return' => '12.05.2022 | 21:27',
          'status' => 'Активен',
          'payments' => [
                [
                  'amount' => 500,
                  'date' => '11.03.2022 | 12:44',
                  'status' => 'Успешно'
                ],
                [
                  'amount' => 300,
                  'date' => '12.03.2022 | 12:47',
                  'status' => 'Ожидается'
                ],
                [
                  'amount' => 1000,
                  'date' => '13.03.2022 | 12:45',
                  'status' => 'Просрочен'
                ],

          ]
        ];
        $this->json_response($res);

      } catch (\Throwable $th) {

        $msg = 'Ошибка при получении текущего займа';
        $this->error_response($msg);

      }
    }

  }