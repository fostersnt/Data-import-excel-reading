<?php

namespace App\Http\Controllers;

use App\Helpers\General;
use App\Mail\TrialMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DataController extends Controller
{
    public function readExistingFile()
    {
        // General::read_school_codes();
        return 'SUCCESS';
        // mail('foster.asante@gwosevo.com', 'TESTING', 'God is good');
        // Mail::to('foster.asante@gwosevo.com')->send(new TrialMail());
        // Path to the existing file
        // $filePath = storage_path('app/files/codes.xlsx');
        // $filePath = storage_path('app/files/at_subs.csv');

        //    $query = DB::connection('server_141')->select("SELECT * FROM Subscribers LIMIT 10");
        //     return $query;

        // Load the file using PhpSpreadsheet
        // $spreadsheet = IOFactory::load($filePath);
        // $sheet = $spreadsheet->getActiveSheet();
        // $data = $sheet->toArray();

        // dd($data);

        // $flatArray = [];
        // foreach ($data as $row) {
        //     foreach ($row as $cell) {
        //         $flatArray[] = $cell;
        //     }
        // }

        // $startIndex = 0; // Starting index
        // $endIndex = 999;   // Ending index
        // $length = $endIndex - $startIndex + 1; // Calculate the length of the slice

        // $subset = array_slice($flatArray, $startIndex, $length);

        // $largeArray = $flatArray; // Creating an array with 47,000 items for demonstration

        // // Size of each chunk
        // $chunkSize = 2000;

        // // Split the array into chunks
        // $chunks = array_chunk($largeArray, $chunkSize);

        // // Print the number of chunks created
        // // echo "Number of chunks: " . json_encode($chunks) . "\n\n";

        // // return $flatArray;

        // foreach ($chunks as $index => $chunk) {
        //     // Create a filename for each chunk
        //     $filename = storage_path('chunk_' . ($index + 1) . '.txt');

        //     // Convert the chunk array to a newline-separated string
        //     // $chunkData = implode(PHP_EOL, $chunk);

        //     $chunkData = implode(',', array_map(function($item) {
        //         return '"' . addslashes($item) . '"';
        //     }, $chunk));

        //     // Write the chunk data to the file
        //     file_put_contents($filename, $chunkData);

        //     // Optional: Output the filename being created
        //     // echo "Created file: " . $filename . "\n";
        // }
        return 'SUCCESS';
        // return count($flatArray);
        // Process the data or pass it to a view
        // return view('result', ['data' => $data]);
    }

    public function excelUpload()
    {
        try {
            if (request()->hasFile('myExcelFile')) {
                $date = date('d_m_Y_G_i_s');
                $file = request()->file('myExcelFile');
                $file_path = $file->move(resource_path('Files'), "excel_upload_$date.xlsx");

                $spreadsheet = IOFactory::load($file_path);
                $result = [];

                foreach ($spreadsheet->getAllSheets() as $sheet) {
                    $sheetData = $sheet->toArray(null, true, true, true);
                    $headers = array_shift($sheetData);

                    Log::info('Sheet data: ' . json_encode($sheetData));

                    foreach ($sheetData as $data) {
                        $item = array_combine($headers, $data);
                        $result[] = $item;
                    }
                }

                // Remove the uploaded file after processing
                unlink($file_path);

                // Chunk the result for processing
                $chunks = count($result) > 100 ? array_chunk($result, 50) : [$result];

                $path = resource_path('ExcelFiles');

                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }

                foreach ($chunks as $key => $chunk) {
                    $chunk_file_name = "$path/temp_$key.xlsx";
                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();

                    // Add headers
                    $col = 1;
                    foreach (array_keys($chunk[0]) as $header) {
                        $sheet->setCellValue([$col++, 1], $header);
                    }

                    // Add data
                    $row = 2;
                    foreach ($chunk as $data) {
                        $col = 1;
                        foreach ($data as $value) {
                            $sheet->setCellValue([$col++, $row], $value);
                        }
                        $row++;
                    }

                    // Save the chunk file
                    $writer = new Xlsx($spreadsheet);
                    $writer->save($chunk_file_name);
                }

                return 'Upload successful!';
            } else {
                return 'No file uploaded. Please try again.';
            }
        } catch (\Throwable $th) {
            Log::error("Upload error: " . $th->getMessage(), ['line' => $th->getLine()]);
            return "ERROR MESSAGE: " . $th->getMessage() . "\nLINE NUMBER: " . $th->getLine();
        }
    }
}
