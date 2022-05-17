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
        return $reader;
    }
}
