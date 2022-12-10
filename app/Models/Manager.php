<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    protected $table = 's_managers';
    protected $guarded = [
        'password'
    ];

    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    public function companies()
    {
        return $this->belongsToMany(
            Company::class,
            's_managers_employers',
            'manager_id',
            'company_id'
        );
    }

    public function credentials()
    {
        return $this->hasMany(Credential::class, 'manager_id');
    }
}
