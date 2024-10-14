<?php

namespace App\Console\Commands;

use App\Helpers\General;
use Illuminate\Console\Command;

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
        // General::read_school_codes();
        // General::read_school_regions();
        // General::read_school_districts();
    }
}
