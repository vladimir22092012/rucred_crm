<?php

class PaymentsController extends Controller
{
    public function fetch()
    {
        $payments = $this->Payments->gets();

        foreach ($payments as $payment){
            $company = $this->companies->get_company($payment->company_id);
            $payment->company_name = $company->name;
        }

        $this->design->assign('payments', $payments);

        return $this->design->fetch('payments.tpl');
    }
}