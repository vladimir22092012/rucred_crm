<?php

class PaymentsController extends Controller
{
    public function fetch()
    {
        return $this->design->fetch('payments.tpl');
    }
}