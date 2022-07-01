<?php

class Requests1cController extends Controller
{
    protected $response;

    public function fetch()
    {
        $get_request = $this->request->get('action', 'string');

        if (!empty($get_request)) {
            $methodName = 'action_' . $get_request;
            if (method_exists($this, $methodName)) {
                $this->$methodName();
            }
        }
    }

    private function action_confirm_payment()
    {
        $uid_payment = $this->request->get('uid');

        $operation = $this->operations->get_operation(['uid' => $uid_payment]);

        if(!empty($operation)){
            $order = $this->orders->get_order($operation->order_id);

            $this->response['success'] = 1;
            $this->response['order_number'] = $order->uid;
        }else{
            $this->response['error'] = 1;
            $this->response['message'] = 'Такой операции нет';
        }
    }

    private function output()
    {
        header('Content-type:application/json');
        echo json_encode($this->response);
        exit;
    }
}