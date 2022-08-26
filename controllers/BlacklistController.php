<?php

class BlacklistController extends Controller
{
    public function fetch()
    {
        if ($this->request->post('run')) {
            $tmp_name = $_FILES['import_file']['tmp_name'];
            $format = \PhpOffice\PhpSpreadsheet\IOFactory::identify($tmp_name);

            if($format == 'Csv'){
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                $reader->setInputEncoding('Windows-1251');
                $reader->setDelimiter(';');
                $reader->setEnclosure('');
                $reader->setSheetIndex(0);
            }else{
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($format);
            }
            $spreadsheet = $reader->load($tmp_name);

            $active_sheet = $spreadsheet->getActiveSheet();

            $first_row = 2;
            $last_row = $active_sheet->getHighestRow();

            for ($row = $first_row; $row <= $last_row; $row++) {

                $client=
                    [
                        'phone' => $active_sheet->getCell('A' . $row)->getValue(),
                        'fio' => strtoupper($active_sheet->getCell('B' . $row)->getValue())
                    ];

                $this->Blacklist->add_person($client);

                if($row % 500 == 0)
                    usleep(300000);
            }

            $this->design->assign('complete', "Файл успешно загружен");
        }

        return $this->design->fetch('blacklist.tpl');
    }
}
