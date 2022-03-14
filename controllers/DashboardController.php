<?php

class DashboardController extends Controller
{
    public function fetch()
    {

return false;

    	return $this->design->fetch('dashboard.tpl');
    }
    
}