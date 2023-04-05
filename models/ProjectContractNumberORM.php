<?php

class ProjectContractNumberORM extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 's_project_contract_number';
    protected $guarded = [];
    public $timestamps = false;

    /**
     * Получаем номер для нового договора
     * @param $group_number
     * @param $company_number
     * @param $loantype_number
     * @param $personal_number
     * @param $user_id
     * @return string
     */
    public static function getNewNumber($group_number, $company_number, $loantype_number, $personal_number, $user_id, $order_id)
    {
        try {
            $count_orders = OrdersORM::query()
                ->where('user_id', '=', $user_id)
                ->where('id', '!=', $order_id)
                ->whereNotIn('status', [8,11,15,16,20])
                ->count();

            $count_contracts = $count_orders + 1;
            $count_contracts = str_pad($count_contracts, 2, '0', STR_PAD_LEFT);
        } catch (Exception $exception) {
            $count_contracts = '01';
        }

        return "$group_number$company_number $loantype_number $personal_number $count_contracts";
    }

    /**
     * Если изменили тариф или работодателя, нужно изменить данные без хвоста
     * @param $number
     * @param $group_number
     * @param $company_number
     * @param $loantype_number
     * @param $personal_number
     * @page $user_id
     * @return string
     */
    public static function refactorNumber($number, $group_number, $company_number, $loantype_number, $personal_number, $user_id, $order_id)
    {
        if ($number) {
            if (isset($number_array[3])) {
                $number_array = explode(' ', $number->uid);
                return "$group_number$company_number $loantype_number $personal_number {$number_array[3]}";
            } else {
                return self::getNewNumber($group_number, $company_number, $loantype_number, $personal_number, $user_id, $order_id);
            }
        } else {
            return self::getNewNumber($group_number, $company_number, $loantype_number, $personal_number, $user_id, $order_id);
        }

    }

}
