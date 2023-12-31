<?php

class CblistController extends Controller
{

    public function fetch()
    {
        if ($this->request->post('run')) {
            if (empty($_FILES['import_file'])) {
                $this->design->assign('error', 'Загрузите файл');
            } else {

                $xml = simplexml_load_file($_FILES['import_file']['tmp_name']);
                $xml = $xml->ИнформЧасть->Раздел2;
                $success = true;

                    foreach ($xml as $value) {

                        $lastname = $value->Участник->СведФЛИП->ФИОФЛИП->Фам;
                        $firstname = $value->Участник->СведФЛИП->ФИОФЛИП->Имя;
                        $patronymic = $value->Участник->СведФЛИП->ФИОФЛИП->Отч;

                        $fio = "$lastname $firstname $patronymic";



                        $passport_serial = json_decode($value->Участник->СведФЛИП->СведДокУдЛичн->СерияДок[0]);
                        $passport_number = json_decode($value->Участник->СведФЛИП->СведДокУдЛичн->НомДок[0]);
                        $birth = ($value->Участник->СведФЛИП->ДатаРождения);
                        $birth = str_replace('/', '-', (string)$birth);
                        $birth = date('Y-m-d', strtotime($birth));


                        $person =
                            [
                                'fio' => $fio,
                                'birth' => $birth,
                                'inn' => json_decode($value->Раздел2->Участник->СведФЛИП->ИИНФЛИП),
                                'passport_serial' => (string)$passport_serial,
                                'passport_number' => (string)$passport_number
                            ];



                        $result = $this->cblist->add_person($person);
                    }
                }
                }


                if ($result == false) {
                    $success == false;
                }

                $this->design->assign('success', $success);



        return $this->design->fetch('cblist.tpl');
    }
}
