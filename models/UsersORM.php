<?php

use App\Helpers\PhoneHelpers;

class UsersORM extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 's_users';
    protected $guarded = [];
    public $timestamps = false;

    protected $casts = [
        'phone_mobile',
    ];

    protected $appends = [
        'companyName',
    ];

    public function orders()
    {
        return $this->hasMany(OrdersORM::class, 'user_id','id');
    }

    public function getPhoneMobileAttribute($phone) {
        return PhoneHelpers::format($phone);
    }

    public function getCompanyNameAttribute() {
        $company = CompaniesORM::find($this->company_id);
        return $company ? $company->name : '';
    }

}
