<?php

error_reporting(-1);
ini_set('display_errors', 'On');
date_default_timezone_set('Europe/Moscow');


chdir(dirname(__FILE__) . '/../');

require __DIR__ . '/../vendor/autoload.php';

class SendYaDiskCron extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->run();
    }

    private function run()
    {

        $crons = $this->YaDiskCron->gets();

        foreach ($crons as $cron){

            $type = '';
            $pak = explode('_', $cron->pak);

            if($cron->pak == 'first_pak'){
                $type = $this->db->placehold("AND `type` not in ('individualnie_usloviya.tpl', 'grafik_obsl_mkr.tpl')");
            }

            if($cron->pak == 'second_pak'){
                $type = $this->db->placehold("AND `type` in ('individualnie_usloviya.tpl', 'grafik_obsl_mkr.tpl')");
            }

            $query = $this->db->placehold("
                SELECT *
                FROM s_scans
                WHERE order_id = ?
                $type
                ", $cron->order_id);

            $this->db->query($query);

            try {

                $this->YaDisk->upload_orders_files($cron->order_id, $pak[0]);
            } catch (Exception $e) {

            }

            $update =
                [
                    'is_complited' => 1
                ];

            $this->YaDiskCron->update($update, $cron->id);
        }
    }
}

new SendYaDiskCron();