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
        'registries',
        'issetOrders',
        'issetDeals'
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

    public function getRegistriesAttribute() {
        return [
            'missing' => count($this->getMissings()) > 0 ? true : false,
            'clients' => $this->is_client == 1 ? true : false,
            'orders' => count($this->getOrders()) > 0 ? true : false,
            'deals' => count($this->getDeals()) > 0 ? true : false,
        ];
    }

    public function getIssetOrdersAttribute() {
        return count($this->getOrders()) > 0 ? 'Да' : 'Нет';
    }

    public function getIssetDealsAttribute() {
        return count($this->getDeals()) > 0 ? 'Да' : 'Нет';
    }

    public function getMissings() {
        return OrdersORM::query()
            ->where('user_id', '=', $this->id)
            ->where('first_loan', '=', 1)
            ->get();
    }

    public function getOrders() {
        return OrdersORM::query()
            ->where('user_id', '=', $this->id)
            ->get();
    }

    public function getDeals() {
        return OrdersORM::query()
            ->where('user_id', '=', $this->id)
            ->whereIn('status', [5, 7, 17, 18, 19])
            ->get();
    }

}
