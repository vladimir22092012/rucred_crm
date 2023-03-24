<?php

use App\Helpers\PhoneHelpers;

class DeleteUsersController extends Controller
{
    public function fetch()
    {
        if ($this->request->method('post')) {
            if ($this->request->post('action', 'string')) {
                $methodName = 'action_' . $this->request->post('action', 'string');
                if (method_exists($this, $methodName)) {
                    $this->$methodName();
                }
            }
        }

        return $this->design->fetch('delete_users.tpl');
    }

    private function action_delete_user() {
        $userIds = $this->request->post('userIds');
        if (count($userIds) > 0) {
            $ordersIds = [];
            $orders = OrdersORM::query()->whereIn('user_id', $userIds)->get();
            foreach ($orders as $order) {
                $ordersIds[] = $order->id;
            }
            $tickets = TicketsORM::query()->whereIn('order_id', $ordersIds)->get();
            $ticketsIds = [];
            foreach ($tickets as $ticket) {
                $ticketsIds[] = $ticket->id;
            }
            OrdersORM::query()->whereIn('id', $ordersIds)->delete();
            NotificationCronORM::query()->whereIn('ticket_id', $ticketsIds)->delete();
            TicketsORM::query()->whereIn('id', $ticketsIds)->delete();
            ContractsORM::query()->whereIn('user_id', $userIds)->delete();
            UsersORM::query()->whereIn('id', $userIds)->delete();
        }
        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_search_users()
    {
        $params = [];
        $query = UsersORM::query();
        $phone = $this->request->post('phone_mobile');
        if (!empty($phone)) {
            $phone = PhoneHelpers::format($phone, 'long_to_small');
            if(strlen($phone) > 11 || strlen($phone) < 11){
                echo json_encode(['error' => 'Проверьте правильность номера, не соответствует кол-во цифр']);
                exit;
            } else {
                $query->where('phone_mobile', '=', $phone);
            }
        }
        $fields = [
            'email',
            'passport_serial',
            'inn',
            'snils'
        ];
        foreach ($fields as $field) {
            if ($param = $this->request->post($field)) {
                if (!empty($param)) {
                    $query->where($field, '=', $param);
                }
            }
        }

        $query->limit = 50;

        $users = $query->get();

        if (count($users) == 0) {
            echo json_encode(['query' => $query->toSql(), 'users' => $users, 'error' => 'Пользователей по заданным параметрам, не найдено, попробуйте изменить условия поиска.']);
            exit;
        } else {
            foreach ($users as $user){
                /**/
            }
            echo json_encode(['success' => 1, 'users' => $users]);
            exit;
        }
    }
}
