<?php

namespace App\Helpers;

use App\Models\District;
use App\Models\Location;
use App\Models\Programme;
use App\Models\Region;
use App\Models\School;
use App\Models\SchoolCategory;
use App\Models\SchoolCode;
use App\Models\SchoolProgramme;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class General
{
    public static function read_schools_and_locations()
    {
        $filePath = storage_path('app/files/Government_Schools.xlsx');
        $spreadsheet = IOFactory::load($filePath);
        // $mySheetNames = ['CAT A', 'CAT B', 'CAT C', 'CAT D', 'APPENDIX_1', 'APPENDIX_6'];
        $mySheetNames = ['CAT A', 'CAT B', 'CAT C', 'CAT D'];
        foreach ($spreadsheet->getSheetNames() as $sheetIndex => $sheetName) {
            // Log::info("\nSHEET NAME === " . $sheetName);
            if (in_array($sheetName, $mySheetNames)) {
                $category = NULL;
                if (str_contains(strtolower($sheetName), 'cat')) {
                    $split = explode(' ', $sheetName);
                    $category = $split[1];
                }

                // $sheet = $spreadsheet->getSheet($sheetIndex);
                $sheet = $spreadsheet->getSheet($sheetIndex);

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                Log::info("\n$sheetName === $highestRow");
                $school = null;

                try {
                    for ($row = 2; $row <= $highestRow; $row++) {
                        $relationship_array = [];

                        $code = trim($sheet->getCell('D' . $row)->getValue());
                        $school_name = $sheet->getCell('E' . $row)->getValue();
                        $gender = $sheet->getCell('G' . $row)->getValue();
                        // Log::info("\nCHECK VALUE === " . $sheet->getCell('P' . $row));
                        $num_of_programs = $sheet->getCell('P' . $row)->getValue();
                        $status = $sheet->getCell('Q' . $row)->getValue();
                        $type = $sheet->getCell('R' . $row)->getValue();

                        $region_name = $sheet->getCell('B' . $row);
                        $district_name = $sheet->getCell('C' . $row);
                        $location_name = $sheet->getCell('F' . $row);

                        if (strlen($code) == 5) {
                            Log::info("\nINVALID SCHOOL CODE === " . $code);
                        }

                        $formatted = intval($num_of_programs);
                        $district = District::query()->updateOrCreate(['name' => $district_name], ['name' => $district_name]);
                        $location = Location::query()->updateOrCreate(['name' => $location_name], ['name' => $location_name]);
                        $region = Region::query()->updateOrCreate(['name' => $region_name], ['name' => $region_name]);

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

                        if ($code != null && $code != '') {
                            //Create a school
                            $school = School::updateOrCreate(
                                ['code' => $code],
                                [
                                    'code' => "$code",
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

                            $unique_result = array_unique($relationship_array);
                            if (count($unique_result) > 0) {
                                foreach ($unique_result as $value) {
                                    $check = SchoolProgramme::query()->where('school_id', $school->id)->where('programme_id', $value)->first();
                                    if ($check == null) {
                                        $school->programme()->attach($unique_result);
                                    }
                                }
                            }
                        }

                        $relationship_array = [];
                    }
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
            if ($sheetName == 'APPENDIX_1') {
                $sheet = $spreadsheet->getSheet($sheetIndex);

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                try {
                    $range = $sheet->rangeToArray('I1:BA1', null, true, true, true);
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
            if ($sheetName == 'APPENDIX_6') {
                $sheet = $spreadsheet->getSheet($sheetIndex);

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                try {
                    $range = $sheet->rangeToArray('H1:N1', null, true, true, true);
                    foreach ($range as $row) {
                        foreach ($row as $columnLetter => $cellValue) {
                            $programme_name = $cellValue;
                            Programme::query()->updateOrCreate(
                                ['name' => $programme_name],
                                ['name' => $programme_name]
                            );
                        }
                    }
                } catch (\Throwable $th) {
                    Log::info("\PROGRAMME NAMES ERROR: ", $th->getMessage() . ", LINE NUMBER: " . $th->getLine());
                }
            }
        }
    }

    //APPENDIX 1 PROGRAMMES
    public static function asign_appendix_1_programmes()
    {
        $filePath = storage_path('app/files/Government_Schools.xlsx');
        $spreadsheet = IOFactory::load($filePath);


        $sheet = $spreadsheet->getSheet(6);

        // Log::info("\nSHEET NAME === " . $name);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        try {
            $relationship_array = [];
            for ($row = 2; $row <= $highestRow; $row++) {
                $range = $sheet->rangeToArray("A$row:$highestColumn$row", null, true, true, true);
                foreach ($range as $rangeRow) {

                    $school_check = School::query()->where('code', $rangeRow['D'])->first();

                    if ($school_check != null) {
                        $rangeRow['I'] != null ? array_push($relationship_array, 9) : null;
                        $rangeRow['J'] != null ? array_push($relationship_array, 10) : null;
                        $rangeRow['K'] != null ? array_push($relationship_array, 11) : null;
                        $rangeRow['L'] != null ? array_push($relationship_array, 12) : null;
                        $rangeRow['M'] != null ? array_push($relationship_array, 13) : null;
                        $rangeRow['N'] != null ? array_push($relationship_array, 14) : null;
                        $rangeRow['O'] != null ? array_push($relationship_array, 15) : null;
                        $rangeRow['P'] != null ? array_push($relationship_array, 16) : null;
                        $rangeRow['Q'] != null ? array_push($relationship_array, 17) : null;
                        $rangeRow['R'] != null ? array_push($relationship_array, 18) : null;
                        $rangeRow['S'] != null ? array_push($relationship_array, 19) : null;
                        $rangeRow['T'] != null ? array_push($relationship_array, 20) : null;
                        $rangeRow['U'] != null ? array_push($relationship_array, 21) : null;
                        $rangeRow['V'] != null ? array_push($relationship_array, 22) : null;
                        $rangeRow['W'] != null ? array_push($relationship_array, 23) : null;
                        $rangeRow['X'] != null ? array_push($relationship_array, 24) : null;
                        $rangeRow['Y'] != null ? array_push($relationship_array, 25) : null;
                        $rangeRow['Z'] != null ? array_push($relationship_array, 26) : null;
                        //Continuation
                        $rangeRow['AA'] != null ? array_push($relationship_array, 27) : null;
                        $rangeRow['AB'] != null ? array_push($relationship_array, 28) : null;
                        $rangeRow['AC'] != null ? array_push($relationship_array, 29) : null;
                        $rangeRow['AD'] != null ? array_push($relationship_array, 30) : null;
                        $rangeRow['AE'] != null ? array_push($relationship_array, 31) : null;
                        $rangeRow['AF'] != null ? array_push($relationship_array, 32) : null;
                        $rangeRow['AG'] != null ? array_push($relationship_array, 33) : null;
                        $rangeRow['AH'] != null ? array_push($relationship_array, 34) : null;
                        $rangeRow['AI'] != null ? array_push($relationship_array, 35) : null;
                        $rangeRow['AJ'] != null ? array_push($relationship_array, 36) : null;
                        $rangeRow['AK'] != null ? array_push($relationship_array, 37) : null;
                        $rangeRow['AL'] != null ? array_push($relationship_array, 38) : null;
                        $rangeRow['AM'] != null ? array_push($relationship_array, 39) : null;
                        $rangeRow['AN'] != null ? array_push($relationship_array, 40) : null;
                        $rangeRow['AO'] != null ? array_push($relationship_array, 41) : null;
                        $rangeRow['AP'] != null ? array_push($relationship_array, 42) : null;
                        $rangeRow['AQ'] != null ? array_push($relationship_array, 43) : null;
                        $rangeRow['AR'] != null ? array_push($relationship_array, 44) : null;
                        $rangeRow['AS'] != null ? array_push($relationship_array, 45) : null;
                        $rangeRow['AT'] != null ? array_push($relationship_array, 46) : null;
                        $rangeRow['AU'] != null ? array_push($relationship_array, 47) : null;
                        $rangeRow['AV'] != null ? array_push($relationship_array, 48) : null;
                        $rangeRow['AW'] != null ? array_push($relationship_array, 49) : null;
                        $rangeRow['AX'] != null ? array_push($relationship_array, 50) : null;
                        $rangeRow['AY'] != null ? array_push($relationship_array, 51) : null;
                        $rangeRow['AZ'] != null ? array_push($relationship_array, 52) : null;
                        $rangeRow['BA'] != null ? array_push($relationship_array, 53) : null;

                        if (count($relationship_array) > 0) {
                            $school_check->programme()->attach($relationship_array);
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            Log::info("\nAPPENDIX PROGRAMME NAMES ERROR: ", $th->getMessage() . ", LINE NUMBER: " . $th->getLine());
        }
    }

    //APPENDIX 6 PROGRAMMES ASSIGNMENTS
    public static function asign_appendix_6_programmes()
    {
        $filePath = storage_path('app/files/Government_Schools.xlsx');
        $spreadsheet = IOFactory::load($filePath);


        $sheet = $spreadsheet->getSheet(11);

        // Log::info("\nSHEET NAME === " . $name);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        try {
            $relationship_array = [];
            for ($row = 2; $row <= $highestRow; $row++) {
                $range = $sheet->rangeToArray("A$row:$highestColumn$row", null, true, true, true);
                foreach ($range as $rangeRow) {

                    $school_check = School::query()->where('code', $rangeRow['D'])->first();

                    if ($school_check != null) {
                        $rangeRow['H'] != null && strtolower($rangeRow['H']) == 'x' ? array_push($relationship_array, 17) : null;
                        $rangeRow['I'] != null && strtolower($rangeRow['I']) == 'x' ? array_push($relationship_array, 17) : null;
                        $rangeRow['J'] != null && strtolower($rangeRow['J']) == 'x' ? array_push($relationship_array, 18) : null;
                        $rangeRow['K'] != null && strtolower($rangeRow['K']) == 'x' ? array_push($relationship_array, 19) : null;
                        $rangeRow['L'] != null && strtolower($rangeRow['L']) == 'x' ? array_push($relationship_array, 20) : null;
                        $rangeRow['M'] != null && strtolower($rangeRow['M']) == 'x' ? array_push($relationship_array, 21) : null;
                        $rangeRow['N'] != null && strtolower($rangeRow['N']) == 'x' ? array_push($relationship_array, 22) : null;

                        if (count($relationship_array) > 0) {
                            foreach ($relationship_array as $value) {
                                $programme_check = SchoolProgramme::query()->where('school_id', $school_check->id)->where('programme_id', $value);
                                if ($programme_check == null) {
                                    $school_check->programme()->attach($value);
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            Log::info("\nAPPENDIX PROGRAMME NAMES ERROR: ", $th->getMessage() . ", LINE NUMBER: " . $th->getLine());
        }
    }

    //APPENDIX 3 PROGRAMMES ASSIGNMENTS
    public static function read_appendix_3_programmes()
    {
        $filePath = storage_path('app/files/Government_Schools.xlsx');
        $spreadsheet = IOFactory::load($filePath);


        $sheet = $spreadsheet->getSheet(8);

        // Log::info("\nSHEET NAME === " . $name);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        try {
            for ($row = 3; $row <= 125; $row++) {
                $range = $sheet->rangeToArray("A$row:$highestColumn$row", null, true, true, true);
                foreach ($range as $rangeRow) {
                    Log::info("\nROW DATA === " . $rangeRow['D']);
                    $school_check = School::query()->where('code', trim($rangeRow['D']))->first();
                    Log::info("\nFOUND SCHOOL === " . json_encode($school_check));
                    if ($school_check != null) {
                        $school_check->update([
                            'is_special_boarding_catchment_area' => 'YES'
                        ]);
                    }
                }
            }
        } catch (\Throwable $th) {
            Log::info("\nAPPENDIX 3 DATA ERROR: ", $th->getMessage() . ", LINE NUMBER: " . $th->getLine());
        }
    }
}
