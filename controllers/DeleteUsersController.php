<?php

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

        if(strlen($phone) > 11 || strlen($phone) < 11){
            echo json_encode(['error' => 'Проверьте правильность номера, не соответствует кол-во цифр']);
            exit;
        }else{

            $user = $this->users->get_user_by_phone($phone);

            $query = $this->db->placehold("
            DELETE us, os
            FROM s_users us
            LEFT JOIN s_orders os ON us.id = os.user_id
            WHERE us.id = ?
            ", $user->id);

            $this->db->query($query);

            echo json_encode(['success' => 1]);
            exit;
        }
    }
}