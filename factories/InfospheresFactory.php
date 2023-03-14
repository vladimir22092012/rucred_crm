<?php

class InfospheresFactory
{
    public static function get($scoringName)
    {
        switch ($scoringName)
        {
            case 'inn':
                return new Inn();
            default:
                return new Exception('This Class Is Not Exist');
        }
    }
}