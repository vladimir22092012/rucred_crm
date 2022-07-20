<?php

class RedirectApiController extends Controller
{
    public function fetch()
    {
        header("Location: viber://pa?chatURI=rucred_bot");
    }
}