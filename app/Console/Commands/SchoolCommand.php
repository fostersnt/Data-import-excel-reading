<?php

namespace App\Console\Commands;

use App\Models\SchoolCategory;
use App\Models\SchoolStatus;
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
            'SHS', 'TVET', 'STEM', 'SHTS',
        ];

        $tracks = [
            'single', 'transitiona (double)'
        ];

        $statuses = [
            'Day', 'Boarding', 'Day/Boarding'
        ];

        $school_categories = [
            'A', 'B', 'C', 'D'
        ];

        // $regions = [
        //     "Greater Accra", "Central", "Western", "Eastern", "Northern", "Upper East", "Upper West", "Volta", "Ashanti", "Bono", "Ahafo", "Bono East", "Western North", "North East", "Oti", "Savannah"
        // ];

        //SCHOOL TYPES
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

        //SCHOOL TRACKS
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

        //SCHOOL STATUSES
        foreach ($statuses as $status) {
            try {
                SchoolStatus::query()->createOrUpdate(
                    ['name' => $status],
                    ['name' => $status]
                );
            } catch (\Throwable $th) {
                $this->info("\nSCHOOL STATUSES === ", $th->getMessage());
            }
        }

        //SCHOOL CATEGORIES
        foreach ($school_categories as $school_category) {
            try {
                SchoolCategory::query()->createOrUpdate(
                    ['name' => $school_category],
                    ['name' => $school_category]
                );
            } catch (\Throwable $th) {
                $this->info("\nSCHOOL CATEGORIES === ", $th->getMessage());
            }
        }

        // //REGIONS
        // foreach ($regions as $region) {
        //     try {
        //         SchoolTrack::query()->createOrUpdate(
        //             ['name' => $region],
        //             ['name' => $region]
        //         );
        //     } catch (\Throwable $th) {
        //         $this->info("\nREGIONS === ", $th->getMessage());
        //     }
        // }
    }
}
