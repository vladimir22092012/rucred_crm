<?php

namespace App\Services;

use SpreadsheetReader;

class FileParserService {
    public function parse($file)
    {
        try {
            $reader = new SpreadsheetReader($file);
        } catch (\Exception $e) {
            return null;
        }
        $reader->ChangeSheet(0);
        $key = null;
        foreach ($reader as $row)
        {
            $key = array_search('Сотрудник', $row, true);
            if ($key) {
                break;
            }
        }
        if (!$key) {
            return null;
        }
        $workers = [];
        foreach ($reader as $row)
        {
            $workers[] = $row[$key];
        }
        return $workers;
    }
}