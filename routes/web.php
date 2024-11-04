<?php

use App\Http\Controllers\DataController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::controller(DataController::class)->group(function(){
    Route::get('data', 'readExistingFile')->name('read.data');
    Route::post('read-excel', 'excelUpload')->name('read.excel');
});

Route::post('upload', function(){
    if (request()->hasFile('mycsv')) {
        $file = file(request()->mycsv);
        $data = array_map('str_getcsv', file(request()->mycsv));
        return $data[1];
        $response = 'File exists';
    } else {
        $response = 'File does not exist';
    }
    return $response;
})->name('upload');
