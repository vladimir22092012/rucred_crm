<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 's_documents';
    protected $guarded = [];
    public $timestamps = false;

    public function companies()
    {
        return $this->hasMany(Company::class, 'company_id','id');
    }

    public function branches()
    {
        return $this->hasMany(Branch::class, 'group_id','id');
    }
}
