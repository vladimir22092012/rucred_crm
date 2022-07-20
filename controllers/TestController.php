<?php

class TestController extends Controller
{
    public function fetch()
    {
        var_dump($this->Soap1c->send_loan(107));
        exit;
    }
}