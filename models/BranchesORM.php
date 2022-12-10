<?php

class BranchesORM extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 's_branches';
    protected $guarded = [];
    public $timestamps = false;

    public function company()
    {
        return $this->hasOne(CompaniesORM::class, 'id','company_id');
    }

    public function group()
    {
        return $this->hasOne(GroupsORM::class, 'id','group_id');
    }
}