<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 's_companies';
    protected $guarded = [];
    public $timestamps = false;

    public function branches()
    {
        return $this->hasMany(Branch::class, 'company_id','id');
    }

    public function docs()
    {
        return $this->hasMany(Doc::class);
    }

    public function group()
    {
        return $this->hasOne(Group::class, 'id','group_id');
    }

    public function managers()
    {
        return $this->belongsToMany(
            Manager::class,
            's_managers_employers',
            'company_id',
            'manager_id'
        );
    }
}
