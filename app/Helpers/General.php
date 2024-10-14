<?php

namespace App\Helpers;

use App\Models\District;
use App\Models\Region;
use App\Models\SchoolCategory;
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
        $category = null;
        foreach ($spreadsheet->getSheetNames() as $sheetIndex => $sheetName) {
            switch ($sheetName) {
                case 'CAT A':
                    $check = SchoolCategory::query()->where('name', 'A')->latest()->first();
                    $category = $check ? $check->id : null;
                    break;
                case 'CAT B':
                    $check = SchoolCategory::query()->where('name', 'B')->latest()->first();
                    $category = $check ? $check->id : null;
                    break;
                case 'CAT C':
                    $check = SchoolCategory::query()->where('name', 'C')->latest()->first();
                    $category = $check ? $check->id : null;
                    break;
                case 'CAT D':
                    $check = SchoolCategory::query()->where('name', 'D')->latest()->first();
                    $category = $check ? $check->id : null;
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

    public static function read_school_districts()
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
                        $district_name = $sheet->getCell('C' . $row);
                        if ($district_name != null && $district_name != '') {
                            District::query()->updateOrCreate(
                                ['name' => $district_name],
                                ['name' => $district_name]
                            );
                        }
                    }
                } catch (\Throwable $th) {
                    Log::info("\nREGIONS ERROR: ", $th->getMessage() . ", LINE NUMBER: " . $th->getLine());
                }
            }
        }
    }

    //READING SCHOOL NAMES
    public static function read_CAT_A_schools()
    {
        $filePath = storage_path('app/files/Government_Schools.xlsx');
        $spreadsheet = IOFactory::load($filePath);
        $mySheetNames = ['CAT A', 'CAT B', 'CAT C', 'CAT D'];
        foreach ($spreadsheet->getSheetNames() as $sheetIndex => $sheetName) {
            if ($sheetName == 'CAT A') {
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
