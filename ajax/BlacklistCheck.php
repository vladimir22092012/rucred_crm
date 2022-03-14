<?php

chdir('..');
require 'autoload.php';

$core = new Core();

$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$patronymic = $_POST['patronymic'];
$phone = $_POST['phone_num'];

$fio = "$lastname $firstname $patronymic";

$person = ["fio" => $fio, "phone" => (int)$phone];

$id = $core->blacklist->search($phone, $fio);

if($id > 0)
{
    echo json_encode(1);
}

