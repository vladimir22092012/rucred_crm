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

    private function action_delete_user()
    {
        $phone = $this->request->post('phone');
        if (!empty($phone)) {
            $phone = PhoneHelpers::format($phone, 'long_to_small');
        }
        if(strlen($phone) > 11 || strlen($phone) < 11){
            echo json_encode(['error' => 'Проверьте правильность номера, не соответствует кол-во цифр']);
            exit;
        } else {

            $users = $this->users->get_users_by_phone($phone);

            if(empty($users)){
                echo json_encode(['error' => 'Такого юзера нет']);
                exit;
            }else{

                foreach ($users as $user){
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

                echo json_encode(['success' => 1]);
                exit;
            }
        }
    }
}
