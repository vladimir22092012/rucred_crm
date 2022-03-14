<?php

class RessetPasswordController extends Controller{


    public function fetch(){
        if ($this->request->method('post'))
        {

        }else
        {

        }

        return $this->design->fetch('resset_password.tpl');
    }

}
