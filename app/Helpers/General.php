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
use App\Models\SpecificTechnicalSubject;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class General
{
    public static function createCategories ()
    {

    }

    public static function read_schools_and_locations()
    {
        $filePath = storage_path('app/files/Government_Schools.xlsx');
        $spreadsheet = IOFactory::load($filePath);
        // $mySheetNames = ['CAT A', 'CAT B', 'CAT C', 'CAT D', 'APPENDIX_1', 'APPENDIX_6'];
        $mySheetNames = ['CAT A', 'CAT B', 'CAT C'];
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
                            $code = "00$code";
                        }
                        if (strlen($code) == 6) {
                            $code = "0$code";
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
                                [
                                    'code' => trim($programme_code),
                                    'name' => $programme_name,
                                    'type_of_programme' => 'SHS/SHTS',
                                    'description' => 'These are programmes offered by both SHS and SHTS schools'
                                    ]
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
                                [
                                    'code' => $programme_code,
                                    'name' => $programme_name,
                                    'type_of_programme' => 'Technical Institution',
                                    'description' => 'These are courses offered by the technical institutions'
                                ]
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
                                [
                                    'name' => $programme_name,
                                    'type_of_programme' => 'STEM',
                                    'description' => 'These are stem courses but some SHS schools also offer these courses'
                                ]
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
        $total_left = [];
        // Log::info("\nSHEET NAME === " . $name);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        try {
            for ($row = 3; $row <= 122; $row++) {

                $range = $sheet->rangeToArray("A$row:$highestColumn$row", null, true, true, true);

                foreach ($range as $rangeRow) {
                    Log::info("\nFIRST CODE === " . $rangeRow['D']);

                    $school_check = School::query()->where('code', trim($rangeRow['D']))->first();

                    if ($school_check != null) {
                        $school_check->update([
                            'is_special_boarding_catchment_area' => 'YES'
                        ]);
                    }
                }

                // $range = $sheet->rangeToArray("A122:$highestColumn"."122", null, true, true, true);

                //     Log::info("\nLAST RECORD === " . json_encode($range));
                //     break;
            }
            Log::info("\nMISSING CODES === " . json_encode($total_left));
        } catch (\Throwable $th) {
            Log::info("\nAPPENDIX 3 DATA ERROR: ", $th->getMessage() . ", LINE NUMBER: " . $th->getLine());
        }
    }

    //APPENDIX 4 PROGRAMMES ASSIGNMENTS
    public static function read_appendix_4_programmes()
    {
        $filePath = storage_path('app/files/Government_Schools.xlsx');
        $spreadsheet = IOFactory::load($filePath);


        $sheet = $spreadsheet->getSheet(9);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $missing_codes = [];

        try {
            for ($row = 2; $row <= 411; $row++) {

                $range = $sheet->rangeToArray("A$row:$highestColumn$row", null, true, true, true);

                foreach ($range as $rangeRow) {
                    $code = trim($rangeRow['D']);
                    $school_check = School::query()->where('code', $code)->first();

                    if ($school_check != null) {
                        $school_check->update([
                            'is_cluster' => 'YES'
                        ]);
                    } else {
                        array_push($missing_codes, $code);
                        $district_name = $rangeRow['C'];
                        $region_name = $rangeRow['B'];

                        $district = District::query()->where('name', 'LIKE', "%$district_name%")->first();
                        $region = Region::query()->where('name', 'LIKE', $region_name)->first();

                        School::query()->create([
                            'code' => "$code",
                            'name' => $rangeRow['E'],
                            'district_id' => $district->id,
                            'region_id' => $region->id,
                            'location_id' => NULL,
                            'gender' => $rangeRow['F'],
                            'category' => $rangeRow['G'],
                            'is_cluster' => 'YES',
                        ]);
                    }
                }
            }
            Log::info("\nMISSING CODES === " . json_encode($missing_codes));
        } catch (\Throwable $th) {
            Log::info("\nAPPENDIX 4 DATA ERROR: ", $th->getMessage() . ", LINE NUMBER: " . $th->getLine());
        }
    }

    //APPENDIX 7 PROGRAMMES ASSIGNMENTS
    public static function read_appendix_7_programmes()
    {
        $filePath = storage_path('app/files/Government_Schools.xlsx');
        $spreadsheet = IOFactory::load($filePath);


        $sheet = $spreadsheet->getSheet(12);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $missing_codes = [];

        try {
            for ($row = 5; $row <= 524; $row++) {

                $range = $sheet->rangeToArray("A$row:$highestColumn$row", null, true, true, true);

                foreach ($range as $rangeRow) {

                    $code = trim($rangeRow['C']);

                    if (strlen($code) == 5) {
                        $code = "00$code";
                    }
                    if (strlen($code) == 6) {
                        $code = "0$code";
                    }

                    $school_check = School::query()->where('code', $code)->first();

                    if ($school_check != null) {
                        $school_check->update([
                            'track' => $rangeRow['F']
                        ]);
                    } else {
                        $district_name = $rangeRow['C'];
                        $region_name = $rangeRow['B'];

                        $location_name = $rangeRow['E'];
                        $location = Location::query()->where('name', 'LIKE', $location_name)->first();

                        if ($location == null) {
                            $location = Location::query()->create(['name' => $location_name]);
                        }

                        School::query()->create([
                            'code' => "$code",
                            'name' => $rangeRow['D'],
                            'location_id' => $location->id,
                            'track' => $rangeRow['F'],
                        ]);
                    }
                }
            }
            Log::info("\nMISSING CODES === " . json_encode($missing_codes));
        } catch (\Throwable $th) {
            Log::info("\nAPPENDIX 4 DATA ERROR: ", $th->getMessage() . ", LINE NUMBER: " . $th->getLine());
        }
    }

    //APPENDIX 7 PROGRAMMES ASSIGNMENTS
    public static function read_appendix_8_programmes()
    {
        $filePath = storage_path('app/files/Government_Schools.xlsx');
        $spreadsheet = IOFactory::load($filePath);


        $sheet = $spreadsheet->getSheet(13);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $missing_codes = [];

        try {
            for ($row = 3; $row <= 415; $row++) {

                $range = $sheet->rangeToArray("A$row:$highestColumn$row", null, true, true, true);

                foreach ($range as $rangeRow) {

                    $code = trim($rangeRow['C']);

                    if (strlen($code) == 5) {
                        $code = "00$code";
                    }
                    if (strlen($code) == 6) {
                        $code = "0$code";
                    }

                    $school_check = School::query()->where('code', $code)->first();

                    if ($school_check != null) {
                        $school_check->update([
                            'track' => $rangeRow['F']
                        ]);
                    } else {
                        $location_name = $rangeRow['F'];
                        $location = Location::query()->where('name', 'LIKE', $location_name)->first();

                        if ($location == null) {
                            $location = Location::query()->create(['name' => $location_name]);
                        }

                        School::query()->create([
                            'code' => "$code",
                            'name' => $rangeRow['D'],
                            'location_id' => $location->id,
                            'track' => $rangeRow['F'],
                        ]);
                    }
                }
            }
            Log::info("\nMISSING CODES === " . json_encode($missing_codes));
        } catch (\Throwable $th) {
            Log::info("\nAPPENDIX 4 DATA ERROR: ", $th->getMessage() . ", LINE NUMBER: " . $th->getLine());
        }
    }

    //APPENDIX 2 PROGRAMMES ASSIGNMENTS
    public static function read_appendix_2_programmes()
    {
        $filePath = storage_path('app/files/Government_Schools.xlsx');
        $spreadsheet = IOFactory::load($filePath);


        $sheet = $spreadsheet->getSheet(7);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        try {
            $programmes = [];

            for ($row = 3; $row <= 178; $row++) {

                $range = $sheet->rangeToArray("E$row:K$row", null, true, true, true);

                foreach ($range as $rangeRow) {

                    $programme1 = $rangeRow['E'];
                    $programme2 = $rangeRow['F'];
                    $programme3 = $rangeRow['G'];
                    $programme4 = $rangeRow['H'];
                    $programme5 = $rangeRow['I'];
                    $programme6 = $rangeRow['J'];
                    $programme7 = $rangeRow['K'];

                    if ($programme1 != null) {
                        array_push($programmes, $programme1);
                    }
                    if ($programme2 != null) {
                        array_push($programmes, $programme2);
                    }
                    if ($programme3 != null) {
                        array_push($programmes, $programme3);
                    }
                    if ($programme4 != null) {
                        array_push($programmes, $programme4);
                    }
                    if ($programme5 != null) {
                        array_push($programmes, $programme5);
                    }
                    if ($programme6 != null) {
                        array_push($programmes, $programme6);
                    }
                    if ($programme7 != null) {
                        array_push($programmes, $programme7);
                    }

                    $specific_subject = SpecificTechnicalSubject::query()->where('name',)->first();

                    if ($specific_subject == null && count($programmes) > 0) {
                        foreach ($programmes as $value) {
                            SpecificTechnicalSubject::query()->updateOrCreate(
                                ['name' => $value],
                                [
                                    'name' => $value,
                                    'programme_code' => '301',
                                ]
                            );
                        }
                    }
                    $programmes = [];
                }
            }
        } catch (\Throwable $th) {
            Log::info("\nAPPENDIX 2 DATA ERROR: ", $th->getMessage() . ", LINE NUMBER: " . $th->getLine());
        }
    }
}
