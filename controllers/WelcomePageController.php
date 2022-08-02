<?php

class WelcomePageController extends Controller
{
    public function fetch()
    {
        $manager_role = $this->manager->role;

        $this->design->assign('manager_role', $manager_role);

        return $this->design->fetch('welcome_page.tpl');
    }
}