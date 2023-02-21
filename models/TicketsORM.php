<?php

class TicketsORM extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 's_tickets';
    protected $guarded = [];
    public $timestamps = false;

    public function theme()
    {
        return $this->hasOne(TicketNotificationsORM::class, 'id', 'theme_id');
    }

}
