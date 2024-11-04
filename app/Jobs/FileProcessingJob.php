<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FileProcessingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    /**
     * Create a new job instance.
     */
    public $file_path;
    public function __construct($path)
    {
        $this->file_path = $path;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $file_path = $this->file_path;

        $spreadsheet = IOFactory::load($file_path);

        $result = [];

        $batch = Bus::batch([])->dispatch();

        foreach ($spreadsheet->getAllSheets() as $sheet) {

            $sheetData = $sheet->toArray(null, true, true, true);

            // dd($sheetData);

            $headers = array_shift($sheetData);

            Log::info('Sheet data: ' . json_encode($sheetData));

            foreach ($sheetData as $data) {

                $item = array_combine($headers, $data);

                $result[] = $item;
            }
        }

        //Remove the file
        unlink($file_path);

        $chunks = count($sheetData) > 100 ? array_chunk($sheetData, 50) : 0;

        foreach ($chunks as $key => $chunk) {
            $chunk_file_name = "/temp_$key.xlsx";
            $path = resource_path("Temp");
            // foreach ($chunk as $key => $value) {
            if (! dir($path)) {
                mkdir($path, 777, true);
            }
            // if (! file_exists($path . $chunk_file_name)) {
            file_put_contents($path . $chunk_file_name, $chunk);
            // }
            // }
            // return $path . $chunk_file_name;

        }
        dd($chunks[0]);
    }
}
