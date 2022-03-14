<?php

class Leadgens extends Core
{
    public function send_pending_postback($click_id, $sub_id) {
        //?utm_source=leadcraft&wm_id=3a110c07-53aa-4a31-a807-7c8b002f4602&clickid=557700e6-6cd4-4e6f-8e95-f229c498e5f6

        //https://api.leadcraft.ru/v1/advertisers/actions?token=421d8f9cb297854371a4b4371fd2413d4a1a0c5bdc7ba681687d60545c7e30d5&actionID=274&status=pending&clickID=[ID КЛИКА]&advertiserID=[ВАШ ID]&reviseDate=[ВАША ДАТА]&price=0
        $reviseDate = date("Y-m-d");
        $link_lead = 'https://api.leadcraft.ru/v1/advertisers/actions?token=421d8f9cb297854371a4b4371fd2413d4a1a0c5bdc7ba681687d60545c7e30d5&actionID=274&status=pending&clickID='.$click_id.'&advertiserID='.$sub_id.'&reviseDate='.$reviseDate;
    
        $ch = curl_init($link_lead);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $res = curl_exec($ch);
        curl_close($ch);
    
        file_put_contents($this->config->root_dir.'logs/leadcraft.txt', date('d-m-Y H:i:s').' pending'.PHP_EOL.$link_lead.PHP_EOL.PHP_EOL.var_export($res).PHP_EOL.PHP_EOL, FILE_APPEND);
        //$this->logging(__METHOD__, 'account_view_zayavka', $link_lead, $res, 'leadcraft.txt');
    }

    public function send_approved_postback($click_id, $sub_id) {
        $reviseDate = date("Y-m-d");
        $link_lead = 'https://api.leadcraft.ru/v1/advertisers/actions?token=421d8f9cb297854371a4b4371fd2413d4a1a0c5bdc7ba681687d60545c7e30d5&actionID=274&status=approved&clickID='.$click_id.'&advertiserID='.$sub_id.'&reviseDate='.$reviseDate;

        $ch = curl_init($link_lead);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $res = curl_exec($ch);
        curl_close($ch);

        file_put_contents($this->config->root_dir.'logs/leadcraft.txt', date('d-m-Y H:i:s').' approved'.PHP_EOL.$link_lead.PHP_EOL.PHP_EOL.var_export($res).PHP_EOL.PHP_EOL, FILE_APPEND);
        //$this->logging(__METHOD__, 'account_view_zayavka', $link_lead, $res, 'leadcraft.txt');
    }

    public function send_cancelled_postback($click_id, $sub_id) {
        //https://api.leadcraft.ru/v1/advertisers/actions?token=421d8f9cb297854371a4b4371fd2413d4a1a0c5bdc7ba681687d60545c7e30d5&actionID=274&status=cancelled&clickID=[ID КЛИКА]&advertiserID=[ВАШ ID]&reviseDate=[ВАША ДАТА]

        $reviseDate = date("Y-m-d");
        $link_lead = 'https://api.leadcraft.ru/v1/advertisers/actions?token=421d8f9cb297854371a4b4371fd2413d4a1a0c5bdc7ba681687d60545c7e30d5&actionID=274&status=cancelled&clickID='.$click_id.'&advertiserID='.$sub_id.'&reviseDate='.$reviseDate;

        $ch = curl_init($link_lead);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $res = curl_exec($ch);
        curl_close($ch);

        file_put_contents($this->config->root_dir.'logs/leadcraft.txt', date('d-m-Y H:i:s').' cancelled'.PHP_EOL.$link_lead.PHP_EOL.PHP_EOL.var_export($res).PHP_EOL.PHP_EOL, FILE_APPEND);
        //$this->logging(__METHOD__, 'account_view_zayavka', $link_lead, $res, 'leadcraft.txt');
    }
}
