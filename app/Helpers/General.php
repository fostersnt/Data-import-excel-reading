<?php

namespace App\Helpers;

use App\Models\District;
use App\Models\Location;
use App\Models\Programme;
use App\Models\Region;
use App\Models\School;
use App\Models\SchoolCategory;
use App\Models\SchoolCode;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class General
{
    public static function read_schools_and_locations()
    {
        $filePath = storage_path('app/files/Government_Schools.xlsx');
        $spreadsheet = IOFactory::load($filePath);
        $mySheetNames = ['CAT A', 'CAT B', 'CAT C', 'CAT D'];
        foreach ($spreadsheet->getSheetNames() as $sheetIndex => $sheetName) {

            if (in_array($sheetName, $mySheetNames)) {

                $split = explode(' ', $sheetName);
                $category = $split[1];

                $sheet = $spreadsheet->getSheet($sheetIndex);
                Log::info("\nSHEET NAME === $sheetName");

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $school = null;

                $relationship_array = [];

                try {
                    for ($row = 2; $row <= $highestRow; $row++) {

                        $code = $sheet->getCell('D' . $row)->getValue();
                        $school_name = $sheet->getCell('E' . $row)->getValue();
                        $gender = $sheet->getCell('G' . $row)->getValue();
                        // Log::info("\nCHECK VALUE === " . $sheet->getCell('P' . $row));
                        $num_of_programs = $sheet->getCell('P' . $row)->getValue();
                        $formatted = intval($num_of_programs);
                        // $formatted = intval($num_of_programs);
                        $status = $sheet->getCell('Q' . $row)->getValue();
                        $type = $sheet->getCell('R' . $row)->getValue();

                        $region_name = $sheet->getCell('B' . $row);
                        $district_name = $sheet->getCell('C' . $row);
                        $location_name = $sheet->getCell('F' . $row);

                        $district = District::query()->updateOrCreate(['name' => $district_name], ['name' => $district_name]);
                        $location = Location::query()->updateOrCreate(['name' => $location_name], ['name' => $location_name]);
                        $region = Region::query()->updateOrCreate(['name' => $region_name], ['name' => $region_name]);

                        //Create a school
                        $school = School::query()->updateOrCreate(
                            ['code' => $code],
                            [
                                'code' => $code,
                                'name' => $school_name,
                                'gender' => $gender,
                                'num_of_programs' => $formatted,
                                'type' => $type,
                                'status' => $status,
                                'district_id' => $district->id,
                                'location_id' => $location->id,
                                'region_id' => $region->id,
                                'category' => $category
                            ]
                        );



                        if (strtolower($sheet->getCell('H' . $row)->getValue()) == 'x') {
                            array_push($relationship_array, 1);
                        }
                        if (strtolower($sheet->getCell('I' . $row)->getValue()) == 'x') {
                            array_push($relationship_array, 2);
                        }
                        if (strtolower($sheet->getCell('J' . $row)->getValue()) == 'x') {
                            array_push($relationship_array, 3);
                        }
                        if (strtolower($sheet->getCell('K' . $row)->getValue()) == 'x') {
                            array_push($relationship_array, 4);
                        }
                        if (strtolower($sheet->getCell('L' . $row)->getValue()) == 'x') {
                            array_push($relationship_array, 5);
                        }
                        if (strtolower($sheet->getCell('M' . $row)->getValue()) == 'x') {
                            array_push($relationship_array, 6);
                        }
                        if (strtolower($sheet->getCell('N' . $row)->getValue()) == 'x') {
                            array_push($relationship_array, 7);
                        }
                        if (strtolower($sheet->getCell('O' . $row)->getValue()) == 'x') {
                            array_push($relationship_array, 8);
                        }

                        $school->programme()->attach($relationship_array);

                        $relationship_array = [];

                        //Programme Association
                        // $myRange = $sheet->rangeToArray("H$row:O$row", null, true, true, true);
                        // $relationship_array = [];
                    }
                    // foreach ($myRange as $myRow) {
                    //     foreach ($myRow as $key => $value) {
                    //         if ($key == 'H' && strtolower($value) == 'x') {
                    //             array_push($relationship_array, 1);
                    //         }

                    //         if (count($relationship_array) > 0) {
                    //             $school->programme()->attach($relationship_array);
                    //         }
                    //         Log::info("\nSCHOOL CODE === $code, PROGRAMME KEY === $key, PROGRAMME VALUE === $value");
                    //         break;
                    //     }
                    //     // break;
                    // }
                } catch (\Throwable $th) {
                    Log::info("n\SCHOOL CODES ERROR: ", $th->getMessage() . ", LINE NUMBER: " . $th->getLine());
                }
            }
        }
    }

    //READING SCHOOL NAMES
    public static function read_school_programmes()
    {
        $filePath = storage_path('app/files/Government_Schools.xlsx');
        $spreadsheet = IOFactory::load($filePath);
        foreach ($spreadsheet->getSheetNames() as $sheetIndex => $sheetName) {
            if ($sheetName == 'CAT A') {
                //Only CAT A is being used since it has same courses as CAT B, C, and D
                $sheet = $spreadsheet->getSheet($sheetIndex);

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                try {
                    $range = $sheet->rangeToArray('H1:O1', null, true, true, true);
                    foreach ($range as $row) {
                        foreach ($row as $columnLetter => $cellValue) {
                            $split = explode('/', $cellValue);
                            $programme_code = $split[1];
                            $programme_name = $split[0];
                            Programme::query()->updateOrCreate(
                                ['code' => $programme_code],
                                ['code' => trim($programme_code), 'name' => $programme_name]
                            );
                            Log::info("\nPROGRAMME CODE: " . $programme_code . ", PROGRAMME NAME: " . $programme_name);
                        }
                    }
                } catch (\Throwable $th) {
                    Log::info("\PROGRAMME NAMES ERROR: ", $th->getMessage() . ", LINE NUMBER: " . $th->getLine());
                }
            }
            if ($sheetName == 'APPENDIX 1') {
                $sheet = $spreadsheet->getSheet($sheetIndex);

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                try {
                    $range = $sheet->rangeToArray('I2:BA2', null, true, true, true);
                    foreach ($range as $row) {
                        foreach ($row as $columnLetter => $cellValue) {
                            $split = explode('/', $cellValue);
                            $programme_code = $split[1];
                            $programme_name = $split[0];
                            Programme::query()->updateOrCreate(
                                ['code' => $programme_code],
                                ['code' => $programme_code, 'name' => $programme_name]
                            );
                        }
                    }
                } catch (\Throwable $th) {
                    Log::info("\PROGRAMME NAMES ERROR: ", $th->getMessage() . ", LINE NUMBER: " . $th->getLine());
                }
            }
        }
    }

    public static function asign_programme_to_school(School $school, $cell_range)
    {
        $filePath = storage_path('app/files/Government_Schools.xlsx');
        $spreadsheet = IOFactory::load($filePath);

        $mySheets = ['CAT A', 'CAT B', 'CAT C', 'CAT D'];

        foreach ($spreadsheet->getSheetNames() as $sheetIndex => $sheetName) {
            if (in_array($sheetName, $mySheets)) {

                $sheet = $spreadsheet->getSheet($sheetIndex);

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                try {
                    $range = $sheet->rangeToArray('H1:O1', null, true, true, true);
                    foreach ($range as $row) {
                        foreach ($row as $columnLetter => $cellValue) {
                            $split = explode('/', $cellValue);
                            $programme_code = $split[1];
                            $programme_name = $split[0];
                            Programme::query()->updateOrCreate(
                                ['code' => $programme_code],
                                ['code' => $programme_code, 'name' => $programme_name]
                            );
                            Log::info("\nPROGRAMME CODE: " . $programme_code . ", PROGRAMME NAME: " . $programme_name);
                        }
                    }
                } catch (\Throwable $th) {
                    Log::info("\PROGRAMME NAMES ERROR: ", $th->getMessage() . ", LINE NUMBER: " . $th->getLine());
                }
            }
            if ($sheetName == 'APPENDIX 1') {
                $sheet = $spreadsheet->getSheet($sheetIndex);

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                try {
                    $range = $sheet->rangeToArray('I2:BA2', null, true, true, true);
                    foreach ($range as $row) {
                        foreach ($row as $columnLetter => $cellValue) {
                            $split = explode('/', $cellValue);
                            $programme_code = $split[1];
                            $programme_name = $split[0];
                            Programme::query()->updateOrCreate(
                                ['code' => $programme_code],
                                ['code' => $programme_code, 'name' => $programme_name]
                            );
                        }
                    }
                } catch (\Throwable $th) {
                    Log::info("\PROGRAMME NAMES ERROR: ", $th->getMessage() . ", LINE NUMBER: " . $th->getLine());
                }
            }
        }
    }
}
