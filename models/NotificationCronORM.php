<?php

class NotificationCronORM extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 's_notifications_cron';
    protected $guarded = [];
    public $timestamps = false;

    public function ticket()
    {
        return $this->hasOne(TicketsORM::class, 'id','ticket_id');
    }

}
