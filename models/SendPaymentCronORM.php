<?php

class SendPaymentCronORM extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 's_send_payment_cron';
    protected $guarded = [];
    public $timestamps = false;
}