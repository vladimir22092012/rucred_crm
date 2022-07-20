<?php

class RedirectApiController extends Controller
{
    public function fetch()
    {
        $registration = $this->request->get('registration');

        if (!empty($registration)){
            header("Location: viber://pa?chatURI=rucred_bot&registration=$registration");
            exit;
        }
    }
}