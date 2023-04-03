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

    const OKB_TYPE_ONE   = '1';
    const OKB_TYPE_TWO   = '2';
    const OKB_TYPE_THREE = '3';

    const OKB_TYPES = [
        self::OKB_TYPE_ONE => 'Заём / Кредит',
        self::OKB_TYPE_TWO => 'Микрозаём',
        self::OKB_TYPE_THREE => 'Кред.линия',
    ];

    const OKB_TYPE_PERCENTS = [
        self::OKB_TYPE_ONE => 24,
        self::OKB_TYPE_TWO => 143,
        self::OKB_TYPE_THREE => 36,
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

    public static function caclulatePdn($order, $in, $okb_story, $schedules) {
        $totalPayment = 0;
        $pdn = 0;
        OkbStoriesORM::query()->where('user_id', '=', $order->user_id)->delete();

        foreach ($okb_story as $key => $story) {
            $period = 0;
            $debts = number_format(str_replace(',', '.', $story['debts']), 2, '.', '');
            $delay = number_format(str_replace(',', '.', $story['delay']), 2, '.', '');
            $okb_story[$key]['debts'] = $debts;
            $okb_story[$key]['delay'] = $delay;
            $temp_percent = (UsersORM::OKB_TYPE_PERCENTS[$story['type']] / 100) / 12;
            if ($story['type'] != 3) {
                if ($story['updated_at']) {
                    $max = date_diff(new DateTime($story['end_date']), new DateTime($story['updated_at']))->days;
                } else {
                    $max = date_diff(new DateTime($story['end_date']), new DateTime(date('d.m.Y')))->days;
                }
                $period = ceil($max / (365 / 12));

                $payment = round(($debts * $temp_percent) / (1 - (1 + $temp_percent) ** -$period), 2);
            } else {
                $array = [
                    (5/100) * ($debts + $delay + $delay),
                    (10/100) * ($debts + $delay)
                ];
                $payment = round(min($array), 2);
            }
            $totalPayment += $payment;
            $okb_story[$key]['period'] = $period;
            $okb_story[$key]['payment'] = $payment;

            OkbStoriesORM::create([
                'user_id' => $order->user_id,
                'type' => $story['type'],
                'updated_at' => $story['updated_at'],
                'start_date' => $story['start_date'],
                'end_date' => $story['end_date'],
                'debts' => $debts,
                'delay' => $delay,
            ]);
        }

        if (count($schedules) > 0) {
            foreach ($schedules as $schedule) {
                if ($schedule->actual != 1) {
                    continue;
                }
                $schedule_array = json_decode($schedule->schedule, true);
                foreach ($schedule_array as $key => $item) {
                    if ($key != 'result') {
                        $totalPayment += $item['pay_sum'];
                        break;
                    }
                }
                break;
            }
        }

        if ($totalPayment && $in) {
            $pdn = round(($totalPayment / $in) * 100, 2);
        }

        return $pdn;
    }

}
