<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DataController extends Controller
{
    public function readExistingFile()
    {
        // Path to the existing file
        $filePath = storage_path('app/files/at_subs.csv');

        //    $query = DB::connection('server_141')->select("SELECT * FROM Subscribers LIMIT 10");
        //     return $query;

        // Load the file using PhpSpreadsheet
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        $flatArray = [];
        foreach ($data as $row) {
            foreach ($row as $cell) {
                $flatArray[] = $cell;
            }
        }

        // $startIndex = 0; // Starting index
        // $endIndex = 999;   // Ending index
        // $length = $endIndex - $startIndex + 1; // Calculate the length of the slice

        // $subset = array_slice($flatArray, $startIndex, $length);

        $largeArray = $flatArray; // Creating an array with 47,000 items for demonstration

        // Size of each chunk
        $chunkSize = 2000;

        // Split the array into chunks
        $chunks = array_chunk($largeArray, $chunkSize);

        // Print the number of chunks created
        // echo "Number of chunks: " . json_encode($chunks) . "\n\n";

        // return $flatArray;

        foreach ($chunks as $index => $chunk) {
            // Create a filename for each chunk
            $filename = storage_path('chunk_' . ($index + 1) . '.txt');

            // Convert the chunk array to a newline-separated string
            // $chunkData = implode(PHP_EOL, $chunk);

            $chunkData = implode(',', array_map(function($item) {
                return '"' . addslashes($item) . '"';
            }, $chunk));

            // Write the chunk data to the file
            file_put_contents($filename, $chunkData);

            // Optional: Output the filename being created
            // echo "Created file: " . $filename . "\n";
        }
        return 'SUCCESS';
        // return count($flatArray);
        // Process the data or pass it to a view
        // return view('result', ['data' => $data]);
    }
}
