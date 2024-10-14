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
        $category = 'N/A';
        foreach ($spreadsheet->getSheetNames() as $sheetIndex => $sheetName) {
            switch ($sheetName) {
                case 'CAT A':
                    $category = 1;
                    break;
                case 'CAT B':
                    $category = 2;
                    break;
                case 'CAT C':
                    $category = 3;
                    break;
                case 'CAT D':
                    $category = 4;
                    break;

                default:
                    $category = 'N/A';
                    break;
            }
            if (in_array($sheetName, $mySheetNames)) {
                $sheet = $spreadsheet->getSheet($sheetIndex);
                Log::info("\nSHEET NAME === $sheetName");

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                try {
                    for ($row = 2; $row <= $highestRow; $row++) {
                        $code = $sheet->getCell('D' . $row);
                        if ($code != null && $code != '') {
                            SchoolCode::query()->updateOrCreate(
                                ['code' => $code],
                                ['code' => $code, 'category_id' => $category]
                            );
                        }
                    }
                } catch (\Throwable $th) {
                    Log::info("n\SCHOOL CODES ERROR: ", $th->getMessage() . ", LINE NUMBER: " . $th->getLine());
                }
            }
            if (strtolower($sheetName) == 'appendix_1') {
                $sheet = $spreadsheet->getSheet($sheetIndex);
                Log::info("\nSHEET NAME === $sheetName");

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                try {
                    for ($row = 3; $row <= $highestRow; $row++) {
                        $code = $sheet->getCell('D' . $row);
                        if ($code != null && $code != '') {
                            SchoolCode::query()->updateOrCreate(
                                ['code' => $code],
                                ['code' => $code]
                            );
                        }
                    }
                } catch (\Throwable $th) {
                    Log::info("n\SCHOOL CODES ERROR: ", $th->getMessage() . ", LINE NUMBER: " . $th->getLine());
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
                        if ($region_name != null && $region_name != '') {
                            Region::query()->updateOrCreate(
                                ['name' => $region_name],
                                ['name' => $region_name]
                            );
                        }
                    }
                } catch (\Throwable $th) {
                    Log::info("\nREGIONS ERROR: ", $th->getMessage() . ", LINE NUMBER: " . $th->getLine());
                }
            }
        }
    }
}
