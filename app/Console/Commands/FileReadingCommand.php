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
        $this->info("DATA EXTRACTION HAS BEGUN");
        General::read_school_programmes();
        General::read_schools_and_locations();
        General::asign_appendix_1_programmes();
        $this->info("DATA EXTRACTION HAS ENDED");
    }
}
