<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
error_reporting(-1);
ini_set('display_errors', 'On');
class TestController extends Controller
{
    public function fetch()
    {
        $this->parseExcell();
        exit;
    }

    private function check_pay_date($date)
    {
        $checkDate = WeekendCalendarORM::where('date', $date->format('Y-m-d'))->first();

        if (!empty($checkDate)) {
            $date->sub(new DateInterval('P1D'));
            $this->check_pay_date($date);
        }

        return $date;
    }

    private function parseExcell()
    {
        $tmp_name = $this->config->root_dir . 'files/clients.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        for ($row = 2; $row <= 16; $row++) {
            $personalNumber = $active_sheet->getCell('B' . $row)->getValue();
            $lastname = $active_sheet->getCell('C' . $row)->getValue();
            $firstname = $active_sheet->getCell('D' . $row)->getValue();
            $patronymic = $active_sheet->getCell('E' . $row)->getValue();

            $birth = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($active_sheet->getCell('M' . $row)->getValue());
            $passport_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($active_sheet->getCell('R' . $row)->getValue());

            $phone = preg_replace("/[^,.0-9]/", '', $active_sheet->getCell('F' . $row)->getValue());
            $email = $active_sheet->getCell('G' . $row)->getValue();
            $group = GroupsORM::where('number', $active_sheet->getCell('I' . $row)->getValue())->first();
            $company = CompaniesORM::where('number', $active_sheet->getCell('J' . $row)->getValue())->first();
            $branches = BranchesORM::where('number', $active_sheet->getCell('K' . $row)->getValue())->first();
            $birth_place = $active_sheet->getCell('L' . $row)->getValue();
            $birth = $birth->format('Y-m-d');
            $serial = $active_sheet->getCell('N' . $row)->getValue();
            $number = $active_sheet->getCell('O' . $row)->getValue();
            $subdivision = $active_sheet->getCell('P' . $row)->getValue();
            $passport_issued = $active_sheet->getCell('Q' . $row)->getValue();
            $passport_date = $passport_date->format('Y-m-d');
            $snils = $active_sheet->getCell('S' . $row)->getValue();
            $inn = $active_sheet->getCell('T' . $row)->getValue();

            $lastname_spouse = $active_sheet->getCell('AJ' . $row)->getValue();
            $firstname_spouse = $active_sheet->getCell('AK' . $row)->getValue();
            $patronymic_spouse = $active_sheet->getCell('AL' . $row)->getValue();
            $phone_spouse = $active_sheet->getCell('AM' . $row)->getValue();

            if(!empty($lastname_spouse))
            {
                $fio_spouse = "$lastname_spouse $firstname_spouse $patronymic_spouse";
                $sex = 1;
            }
            else
            {
                $fio_spouse = null;
                $phone_spouse = null;
                $sex = 0;
            }

            $userData = [
                'firstname' => strtoupper($firstname),
                'lastname' => strtoupper($lastname),
                'patronymic' => strtoupper($patronymic),
                'personal_number' => $personalNumber,
                'email' => $email,
                'group_id' => $group->id,
                'branche_id' => $branches->id,
                'company_id' => $company->id,
                'passport_serial' => $serial.'-'.$number,
                'subdivision_code' => $subdivision,
                'passport_issued' => $passport_issued,
                'passport_date' => date('Y-m-d', strtotime($passport_date)),
                'snils' => $snils,
                'inn' => $inn,
                'birth' => $birth,
                'sex' => $sex,
                'fio_spouse' => $fio_spouse,
                'phone_spouse' => $phone_spouse,
                'birth_place' => $birth_place,
                'password' => '',
                'phone_mobile' => $phone,
                'canSendOnec' => 1,
                'canSendYaDisk' => 1,
                'stage_registration' => 8
            ];

            $userId = UsersORM::insertGetId($userData);

            $regaddress = [];
            $regaddress['adressfull'] = $active_sheet->getCell('A' . $row)->getValue();
            $regaddress['zip'] = $active_sheet->getCell('U' . $row)->getValue();

            if(!empty($active_sheet->getCell('V' . $row)->getValue()))
            {
                $regRegion = explode(' ', $active_sheet->getCell('V' . $row)->getValue());

                $regaddress['region'] = $regRegion[0];
                $regaddress['region_type'] = $regRegion[1];
            }

            $regCity = explode(' ', $active_sheet->getCell('X' . $row)->getValue());

            $regaddress['city'] = $regCity[1];
            $regaddress['city_type'] = $regCity[0];

            if(!empty($active_sheet->getCell('Y' . $row)->getValue()))
            {
                $faktRegion = explode(' ', $active_sheet->getCell('Y' . $row)->getValue());

                $faktaddress['region'] = $faktRegion[0];
                $faktaddress['region_type'] = $faktRegion[1];
            }


            $regaddress['house'] = $active_sheet->getCell('Z' . $row)->getValue();
            $regaddress['room'] = empty($active_sheet->getCell('AA' . $row)->getValue()) ?? '';

            $faktaddress = [];
            $faktaddress['adressfull'] = $active_sheet->getCell('AB' . $row)->getValue();
            $faktaddress['zip'] = $active_sheet->getCell('AC' . $row)->getValue();

            if(!empty($active_sheet->getCell('AD' . $row)->getValue()))
            {
                $faktRegion = explode(' ', $active_sheet->getCell('AD' . $row)->getValue());

                $faktaddress['region'] = $faktRegion[0];
                $faktaddress['region_type'] = $faktRegion[1];
            }

            $faktCity = explode(' ', $active_sheet->getCell('AF' . $row)->getValue());

            $faktaddress['city'] = $faktCity[1];
            $faktaddress['city_type'] = $faktCity[0];

            if(!empty($active_sheet->getCell('AG' . $row)->getValue()))
            {
                $faktStreet = explode(' ', $active_sheet->getCell('AG' . $row)->getValue());

                $faktaddress['street'] = $faktStreet[1];
                $faktaddress['street_type'] = $faktStreet[0];
            }


            $faktaddress['house'] = $active_sheet->getCell('AH' . $row)->getValue();
            $faktaddress['room'] = empty($active_sheet->getCell('AI' . $row)->getValue()) ?? '';

            $regAddrId = $this->Addresses->add_address($regaddress);
            $faktAddrId = $this->Addresses->add_address($faktaddress);

            UsersORM::where('id', $userId)->update(['regaddress_id' => $regAddrId, 'faktaddress_id' => $faktAddrId]);

            $number_acc = $active_sheet->getCell('AN' . $row)->getValue();
            $bik = $active_sheet->getCell('AO' . $row)->getValue();
            $name = $active_sheet->getCell('AP' . $row)->getValue();

            $fio = strtoupper($lastname).' '.strtoupper($firstname).' '.strtoupper($patronymic);


            $requisite =
                [
                    'user_id' => $userId,
                    'name' => $name,
                    'bik' => $bik,
                    'number' => $number_acc,
                    'holder' => $fio,
                    'correspondent_acc' => $active_sheet->getCell('AQ' . $row)->getValue(),
                    'default' => 1
                ];

            RequisitesORM::insert($requisite);
        }
    }
}