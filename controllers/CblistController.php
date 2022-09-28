<?php

class CblistController extends Controller
{
    public $import_file_dir = 'files/import/';
    public $import_file = 'cblist.xml';

    public function fetch()
    {
        if (!is_writable($this->import_files_dir))
            $this->design->assign('message_error', 'no_permission');

        if (empty($import_file)) {
            $this->design->assign('error', 'Загрузите файл');
        } else {
            $uploaded_name = $this->request->files("import_file", "tmp_name");

            $xml = simplexml_load_file($uploaded_name);


            $success = true;

            foreach ($xml->children() as $value) {

                $person =
                    [
                        'fio' => $value->ФИОФЛИП,
                        'data_birth' => date('Y-m-d', strtotime($value->ДатаРождения)),
                        'inn' => $value->ИИНФЛИП,
                        'number_passport' => $value->НомДок
                    ];

                $result = $this->cblist->add_person($person);
            }
            var_dump();

            if ($result == false) {
                $success == false;
            }

            $this->design->assign('success', $success);
        }
        return $this->design->fetch('cblist.tpl');
    }
}
