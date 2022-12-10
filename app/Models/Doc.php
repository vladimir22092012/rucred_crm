<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doc extends Model
{
    protected $table = 's_docs';
    protected $guarded = [];
    public $timestamps = false;

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
