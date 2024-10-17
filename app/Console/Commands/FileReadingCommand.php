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
        General::createCategories();
        // General::read_schools_and_locations();
        // General::read_school_programmes();
        // General::asign_appendix_1_programmes();
        // General::asign_appendix_6_programmes();
        // General::read_appendix_3_programmes();
        // General::read_appendix_4_programmes();
        // General::read_appendix_7_programmes();
        // General::read_appendix_8_programmes();
        // General::read_appendix_2_programmes();
        $this->info("DATA EXTRACTION HAS ENDED");
    }
}
