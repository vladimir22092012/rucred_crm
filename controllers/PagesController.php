<?php

class PagesController extends Controller
{
    public function fetch()
    {

return false;
    	
        $pages = $this->pages->get_pages();
        $this->design->assign('pages', $pages);
        
        return $this->design->fetch('pages.tpl');
    }
    
}