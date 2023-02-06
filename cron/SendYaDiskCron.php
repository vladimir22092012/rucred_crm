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

        foreach ($crons as $cron) {

            $order = $this->orders->get_order($cron->order_id);

            $canSendYaDiskOrder = OrdersORM::select('id', 'canSendYaDisk')->where('id', $cron->order_id)->first();
            $canSendYaDiskUser = UsersORM::select('id', 'canSendYaDisk')->where('id', $order->user_id)->first();

            if (empty($order)) {
                $this->complete($cron->id);
                continue;
            }

            $type = '';
            $pak = explode('_', $cron->pak);

            if ($cron->pak == 'first_pak') {
                $type = $this->db->placehold("AND `type` not in (
                'individualnie_usloviya.tpl', 
                'grafik_obsl_mkr.tpl', 
                'ind_usloviya_online.tpl',
                'zayavlenie_zp_v_schet_pogasheniya_mrk.tpl',
                'perechislenie_zaemnih_sredstv.tpl',
                'obshie_uslovia.tpl')");

                if ($canSendYaDiskUser->canSendYaDisk == 0 || $canSendYaDiskOrder->canSendYaDisk == 0) {
                    $this->complete($cron->id);
                    continue;
                }
            }

            if ($cron->pak == 'second_pak') {
                $type = $this->db->placehold("AND `type` in (
                'individualnie_usloviya.tpl', 
                'grafik_obsl_mkr.tpl', 
                'ind_usloviya_online.tpl',
                'zayavlenie_zp_v_schet_pogasheniya_mrk.tpl',
                'perechislenie_zaemnih_sredstv.tpl',
                'obshie_uslovia.tpl')");

                if ($canSendYaDiskUser->canSendYaDisk == 0 || $canSendYaDiskOrder->canSendYaDisk == 0) {
                    $this->complete($cron->id);
                    continue;
                }
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

            $this->complete($cron->id);
        }
    }

    private function complete($cronId)
    {
        $this->YaDiskCron->update(['is_complited' => 1], $cronId);
    }
}

new SendYaDiskCron();