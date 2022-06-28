<?php

ini_set('max_execution_time', 300);
class YaDisk extends Core
{
    protected $token;
    protected $disk;

    public function __construct()
    {
        parent::__construct();
        $this->token = 'AQAAAABcOalaAADLWxIYdswB4kYFjIrgW6xGURU';
        $this->disk = new Arhitector\Yandex\Disk($this->token);
    }

    public function upload_orders_files($order_id, $upload_scans)
    {
        $order = $this->orders->get_order($order_id);

        $fio = $order->lastname . ' ' . mb_substr($order->firstname, 0, 1) . mb_substr($order->patronymic, 0, 1);
        $translit_lastname = $this->translit($order->lastname);
        $employer = explode(' ', $order->uid);
        $employer = "$employer[0]$employer[1]";
        $date = date('Y-m-d', strtotime($order->probably_start_date));
        $bank = ($order->settlement_id == 2) ? 'МИнБанк' : 'РосДорБанк';

        if ($upload_scans == 1) {
            $upload_files = $this->Scans->get_scans_by_order_id($order_id);
        } else {
            $upload_files = $this->Documents->get_documents(['order_id' => $order_id]);
        }

        try {
            $resource = $this->disk->getResource('disk:/RC3100 CRM Data/3102 Loans/' . $order->personal_number . ' ' . $translit_lastname . '/');
            $resource->create();
        } catch (Exception $e) {

        }

        foreach ($upload_files as $document) {

            if ($upload_scans == 1)
                $type = $document->type;
            else
                $type = $document->template;

            if ($type == 'individualnie_usloviya.tpl') {
                $file_name = $fio . ' - Договор микрозайма ' . $order->uid . ' ' . "($date)" . ' ' . $bank;
            }

            if ($type == 'soglasie_na_obr_pers_dannih.tpl') {
                $file_name = $fio . ' - Согласие на обработку ПД ' . $order->uid . ' ' . "($date)";
            }

            if ($type == 'soglasie_rdb.tpl') {
                $file_name = $fio . ' - Согласие на идентификацию через банк РДБ ' . $order->uid . ' ' . "($date)";
            }

            if ($type == 'soglasie_minb.tpl') {
                $file_name = $fio . ' - Согласие на идентификацию через банк МИНБ ' . $order->uid . ' ' . "($date)";
            }

            if ($type == 'soglasie_rabotadatelu.tpl') {
                $file_name = $fio . ' - Согласие на обработку и передачу ПД работодателю ' . $employer . ' ' . "($date)";
            }

            if ($type == 'soglasie_rukred_rabotadatel.tpl') {
                $file_name = $fio . " - Согласие работодателю $employer на обработку и передачу ПД " . "($date)";
            }

            if ($type == 'soglasie_na_kred_otchet.tpl') {
                $file_name = $fio . " - Согласие на запрос КО " . "($date)";
            }

            if ($type == 'zayavlenie_na_perechislenie_chasti_zp.tpl') {
                $file_name = $fio . " - Обязательство подать заявление работодателю $employer  на перечисление" . "($date)";
            }

            if ($type == 'zayavlenie_zp_v_schet_pogasheniya_mrk.tpl') {
                $file_name = $fio . " - Заявление работодателю $employer  на перечисление по микрозайму " . "($date)";
            }

            if (isset($file_name)) {
                $file_name = $this->translit($file_name);
                $resource = $this->disk->getResource('disk:/RC3100 CRM Data/3102 Loans/' . $order->personal_number . ' ' . $translit_lastname . '/' . $file_name . '.pdf');

                if ($upload_scans == 1)
                    $resource->upload($this->config->root_url . '/files/users/' . $order->user_id . '/' . $document->name);
                else{
                    $tpl = $this->design->fetch('pdf/' . $document->template);
                    $this->pdf->create($tpl, $document->name, $document->template, $download = false, $yandex = $file_name);
                    $resource->upload(ROOT . '/files/users/'. $file_name.'.pdf');
                    unlink(ROOT . '/files/users/'. $file_name.'.pdf');
                }
            }
            sleep(5);
        }
    }

    private function translit($value)
    {
        $converter = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
            'Е' => 'E', 'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
            'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
            'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'Ch',
            'Ш' => 'Sh', 'Щ' => 'Sch', 'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        );

        $value = strtr($value, $converter);
        return $value;
    }
}