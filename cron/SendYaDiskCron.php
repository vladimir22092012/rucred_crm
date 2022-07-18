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
                SELECT `type`
                FROM s_scans
                WHERE order_id = ?
                $type
                ", $cron->order_id);

            $this->db->query($query);
            $scans = $this->db->results();

            $users_docs = $this->Documents->get_documents(['order_id' => $cron->order_id, $cron->pak => 1]);

            try {
                $upload_scans = 0;

                if (count($scans) == count($users_docs))
                    $upload_scans = 1;

                $this->YaDisk->upload_orders_files($cron->order_id, $upload_scans, $pak[0]);
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