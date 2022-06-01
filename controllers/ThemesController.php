<?php

class ThemesController extends Controller
{
    public function fetch()
    {
        return $this->design->fetch('themes.tpl');
    }
}