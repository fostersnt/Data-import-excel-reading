<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class General
{
    public static function read_excel_file()
    {
        $filePath = storage_path('app/files/Government_Schools.xlsx');
        // Load the spreadsheet
        $spreadsheet = IOFactory::load($filePath);

        // Iterate through each sheet
        foreach ($spreadsheet->getSheetNames() as $sheetIndex => $sheetName) {

            // Get the sheet
            if ($sheetIndex == 1) {
                $sheet = $spreadsheet->getSheet($sheetIndex);
                Log::info("\nSHEET INDEX === $sheetIndex, SHEET NAME === $sheetName");
                // Get the highest row and column
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Loop through each row in the sheet
                // for ($row = 1; $row <= $highestRow; $row++) {
                //     $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

                //     $col1 = $rowData[0][0] ?? null;
                //     $col2 = $rowData[0][1] ?? null;
                //     $col3 = $rowData[0][2] ?? null;
                // }
            }
        }
    }
}
