<?php

class Html2PdfController extends Controller
{
    public function fetch()
    {
        $user_id = $this->request->get('user_id');
        $contract_id = $this->request->get('contract_id');

        switch ($document_name = $this->request->get('document_name')):

            case 'polis_strahovaniya_preview':
                $document_template = 'polis-strahovaniya-preview.tpl';
                $this->polis_strahovaniya($user_id, $document_name, $document_template, $contract_id);
                break;

            case 'dopolnitelnoe-soglashenie-o-prolongatsii':
                $document_template = 'dopolnitelnoe-soglashenie-o-prolongatsii.tpl';
                $this->dop_soglashenie_prolongation($user_id, $document_template, $contract_id);
                break;

            case 'strahovka-pri-zakritii':
                $document_template = 'polis-zakritie.tpl';
                $this->close_insurance($user_id, $document_template, $contract_id);
                break;

        endswitch;

    }

    private function polis_strahovaniya($user_id, $document_name, $document_template, $contract_id)
    {
        $user = $this->users->get_user($user_id);

        $contract = $this->contracts->get_contract($contract_id);

        foreach ($user as $param_name => $param_value) {
            $this->design->assign($param_name, $param_value);
        }

        foreach ($contract as $param_name => $param_value) {
            $this->design->assign($param_name, $param_value);
        }

        $insurance_summ = 0;

        if ($contract->amount <= 2000)
            $insurance_summ = 99;
        elseif ($contract->amount <= 5000)
            $insurance_summ = 450;
        elseif ($contract->amount <= 7000)
            $insurance_summ = 650;
        elseif ($contract->amount <= 10000)
            $insurance_summ = 850;
        else
            $insurance_summ = 900;

        $this->design->assign('insurance_summ', $insurance_summ);

        $regaddress_full = "$user->Regregion $user->Regregion_shorttype $user->Regdistrict $user->Regcity 
        $user->Regcity_shorttype $user->Regstreet $user->Regbuilding $user->Reghousing $user->Regroom";

        $this->design->assign('regaddress_full', $regaddress_full);

        $inssurance_date_end = date('d.m.Y', strtotime('+30 day'));

        $this->design->assign('inssurance_date_end', $inssurance_date_end);

        $tpl = $this->design->fetch('pdf/' . $document_template);

        $this->pdf->create($tpl, $document_name, $document_template);
    }

    private function dop_soglashenie_prolongation($user_id, $document_template, $contract_id)
    {
        $user = $this->users->get_user($user_id);

        $contract = $this->contracts->get_contract($contract_id);

        foreach ($user as $param_name => $param_value) {
            $this->design->assign($param_name, $param_value);
        }

        foreach ($contract as $param_name => $param_value) {
            $this->design->assign($param_name, $param_value);
        }

        $return_amount_percents = ($contract->amount * $contract->base_percent * $contract->period) / 100;


        $this->design->assign('return_amount_percents', $return_amount_percents);

        $tpl = $this->design->fetch('pdf/' . $document_template);

        $this->pdf->create($tpl, 'Дополнительное соглашение о пролонгации', $document_template);
    }

    private function close_insurance($user_id, $document_template, $contract_id)
    {
        $user = $this->users->get_user($user_id);

        $contract = $this->contracts->get_contract($contract_id);

        foreach ($user as $param_name => $param_value) {
            $this->design->assign($param_name, $param_value);
        }

        foreach ($contract as $param_name => $param_value) {
            $this->design->assign($param_name, $param_value);
        }

        $now_date = date('d.m.Y');

        $this->design->assign('now_date', $now_date);

        $tpl = $this->design->fetch('pdf/' . $document_template);

        $this->pdf->create($tpl, 'Страховка при закрытии', $document_template);

    }
}