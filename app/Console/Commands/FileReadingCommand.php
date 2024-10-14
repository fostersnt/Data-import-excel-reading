<?php

namespace App\Console\Commands;

use App\Helpers\General;
use App\Models\School;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FileReadingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'read:file';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command reads excel or csv files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // General::read_school_regions();
        // General::read_school_districts();
        // General::read_schools_and_locations();
        // General::read_school_programmes();
        $school = School::query()->first();
        Log::info("\nPROGRAMMES FOR " . $school->name . " === " . json_encode($school->programme));
        // $school->programme()->attach([1, 3]);
    }
}
