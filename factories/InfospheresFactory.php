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
            case 'fns':
                return new Fns();
            case 'fssp':
                return new Fssp();
            case 'cbrf':
                return new CbRf();
            default:
                return new Exception('This Class Is Not Exist');
        }
    }
}