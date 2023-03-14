<?php

class InfospheresFactory
{
    public static function get($scoringName)
    {
        switch ($scoringName)
        {
            case 'inn':
                return new Inn();
            case 'fms':
                return new Fms();
            case 'fmsdb':
                return new FmsDb();
            case 'mvd':
                return new Mvd();
            default:
                return new Exception('This Class Is Not Exist');
        }
    }
}