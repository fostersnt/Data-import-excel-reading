<?php

namespace App\Console\Commands;

use App\Models\SchoolTrack;
use App\Models\SchoolType;
use Illuminate\Console\Command;

class SchoolCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:school-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $types = [
            'tvet', 'regular', 'stem', 'special (Catchment area)', 'special (cluster)', 'special (hearing & visually impaired)',
        ];

        $tracks = [
            'single', 'transitiona (double)'
        ];

        foreach ($types as $type) {
            try {
                SchoolType::query()->createOrUpdate(
                    ['name' => $type],
                    ['name' => $type]
                );
            } catch (\Throwable $th) {
                $this->info("\nSCHOOL TYPES === ", $th->getMessage());
            }
        }

        foreach ($tracks as $track) {
            try {
                SchoolTrack::query()->createOrUpdate(
                    ['name' => $track],
                    ['name' => $track]
                );
            } catch (\Throwable $th) {
                $this->info("\nSCHOOL TRACKS === ", $th->getMessage());
            }
        }
    }
}
