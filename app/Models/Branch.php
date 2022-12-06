<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 's_branches';
    protected $guarded = [];
    public $timestamps = false;

    public function company()
    {
        return $this->hasOne(Company::class, 'id','company_id');
    }

    public function group()
    {
        return $this->hasOne(Group::class, 'id','group_id');
    }
}
