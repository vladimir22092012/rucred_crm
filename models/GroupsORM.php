<?php

class GroupsORM extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 's_groups';
    protected $guarded = [];
    public $timestamps = false;

    public function companies()
    {
        return $this->hasMany(CompaniesORM::class, 'company_id','id');
    }

    public function branches()
    {
        return $this->hasMany(BranchesORM::class, 'group_id','id');
    }
}