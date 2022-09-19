<?php

class RedirectApiController extends Controller
{
    public function fetch()
    {
        $token = $this->request->get('user_id');

        header("Location: viber://pa?chatURI=".$this->config->viber_bot."&text=registration $token");
        die();
    }
}