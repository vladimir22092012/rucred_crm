<?php

use Illuminate\Database\Eloquent\Model;

class EmployeeORM extends Model
{
    protected $table = 's_managers';

    protected $guarded = [
        'password'
    ];

    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';
}
