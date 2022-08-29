<?php

class RedirectApiController extends Controller
{
    public function fetch()
    {
        $user_id = $this->request->get('user_id');

        header("Location: viber://pa?chatURI=".$this->config->viber_bot."&text=registration $user_id");
        die();
    }
}