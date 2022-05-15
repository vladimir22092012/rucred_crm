<?php

namespace App\Services;

use SpreadsheetReader;

class FileParserService
{
    public function parse($file)
    {
        try {
            $reader = new SpreadsheetReader($file);
        } catch (\Exception $e) {
            return null;
        }
        $reader->ChangeSheet(0);
        $key = false;
        foreach ($reader as $row) {
            $key = array_search('Сотрудник', $row, true);
            if (is_int($key)) {
                break;
            }
        }
        if ($key === false) {
            return null;
        }
        $workers = [];
        foreach ($reader as $row) {
            $workers[] = $row[$key];
        }
        return $workers;
    }
}
