<?php

chdir('..');
require 'autoload.php';

        $core = new Core();

        $fio = '';
        $phone = 0;

        if($_POST['lastname']) {
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $patronymic = $_POST['patronymic'];
            $phone = $_POST['phone_num'];

            $fio = "$lastname $firstname $patronymic";

            $person = ["fio" => $fio, "phone" => (int)$phone];

            $id = $core->blacklist->search($phone, $fio);

            if ($id) {
                $core->blacklist->delete_person($id);
            } else {
                $core->blacklist->add_person($person);
            }
        }
