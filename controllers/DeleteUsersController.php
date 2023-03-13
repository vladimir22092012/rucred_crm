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
            $users = UsersORM::query()->whereIn('id', $userIds);
            foreach ($users as $user) {
                $orders = $this->orders->get_orders(['user_id' => $user->id]);
                $this->orders->delete_orders_by_user_id($user->id);
                $this->contracts->delete_contracts_by_user_id($user->id);
                $this->users->delete_user($user->id);
                $this->contacts->delete($user->id);
                foreach ($orders as $order){
                    $tickets = $this->tickets->get_by_order_id($order->order_id);

                    foreach ($tickets as $ticket){
                        $this->NotificationsCron->delete_by_ticket_id($ticket->id);
                    }

                    $this->tickets->delete_by_order($order->order_id);
                }
            }
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
