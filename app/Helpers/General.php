<?php

namespace App\Helpers;

use App\Models\Region;
use App\Models\SchoolCode;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class General
{
    public static function read_school_codes()
    {
        $filePath = storage_path('app/files/Government_Schools.xlsx');
        $spreadsheet = IOFactory::load($filePath);
        $mySheetNames = ['CAT A', 'CAT B', 'CAT C', 'CAT D'];
        foreach ($spreadsheet->getSheetNames() as $sheetIndex => $sheetName) {
            if (in_array($sheetName, $mySheetNames)) {
                $sheet = $spreadsheet->getSheet($sheetIndex);
                Log::info("\nSHEET NAME === $sheetName");

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                try {
                    for ($row = 2; $row <= $highestRow; $row++) {
                        $code = $sheet->getCell('B' . $row);
                        SchoolCode::query()->updateOrCreate(
                            ['name' => $code],
                            ['name' => $code]
                        );
                    }
                } catch (\Throwable $th) {
                    Log::info("\SCHOOL CODES ERROR: ", $th->getMessage() . ", LINE NUMBER: " . $th->getLine());
                }
            }
        }
    }

    public static function read_school_regions()
    {
        $filePath = storage_path('app/files/Government_Schools.xlsx');
        $spreadsheet = IOFactory::load($filePath);
        $mySheetNames = ['CAT A', 'CAT B', 'CAT C', 'CAT D'];
        foreach ($spreadsheet->getSheetNames() as $sheetIndex => $sheetName) {
            if (in_array($sheetName, $mySheetNames)) {
                $sheet = $spreadsheet->getSheet($sheetIndex);
                Log::info("\nSHEET NAME === $sheetName");

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                try {
                    for ($row = 2; $row <= $highestRow; $row++) {
                        $region_name = $sheet->getCell('B' . $row);
                        Region::query()->updateOrCreate(
                            ['name' => $region_name],
                            ['name' => $region_name]
                        );
                    }
                } catch (\Throwable $th) {
                    Log::info("\nREGIONS ERROR: ", $th->getMessage() . ", LINE NUMBER: " . $th->getLine());
                }
            }
        }
    }
}
