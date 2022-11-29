<?php

class CompaniesORM extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 's_companies';
    protected $guarded = [];
    public $timestamps = false;

    public function branches()
    {
        return $this->hasMany(BranchesORM::class, 'company_id','id');
    }

    public function group()
    {
        return $this->hasOne(GroupsORM::class, 'id','group_id');
    }

    public function employees()
    {
        return $this->belongsToMany(
            EmployeeORM::class,
            's_managers_employers',
            'company_id',
            'manager_id'
        );
    }
}
