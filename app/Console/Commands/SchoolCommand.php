<?php

namespace App\Console\Commands;

use App\Models\Programme;
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
    protected $signature = 'school:command';

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

        $genders = [
            'Boys', 'Girls', 'Mixed'
        ];

        $programmes = [
            101 => 'AGRICULTURE',
            201 => 'BUSINESS',
            301 => 'TECH',
            401 => 'HOME ECONOMICS',
            402 => 'VISUAL ARTS',
            501 => 'GENERAL ARTS',
            502 => 'GENERAL SCIENCE',
            503 => 'STEM'
        ];

        //SCHOOL TYPES
        foreach ($types as $type) {
            try {
                SchoolType::query()->updateOrCreate(
                    ['name' => $type],
                    ['name' => $type]
                );
            } catch (\Throwable $th) {
                $this->info("\nSCHOOL TYPES === ". $th->getMessage());
            }
        }

        //SCHOOL TRACKS
        foreach ($tracks as $track) {
            try {
                SchoolTrack::query()->updateOrCreate(
                    ['name' => $track],
                    ['name' => $track]
                );
            } catch (\Throwable $th) {
                $this->info("\nSCHOOL TRACKS === ". $th->getMessage());
            }
        }

        //SCHOOL STATUSES
        foreach ($statuses as $status) {
            try {
                SchoolStatus::query()->updateOrCreate(
                    ['name' => $status],
                    ['name' => $status]
                );
            } catch (\Throwable $th) {
                $this->info("\nSCHOOL STATUSES === ". $th->getMessage());
            }
        }

        //SCHOOL CATEGORIES
        foreach ($school_categories as $school_category) {
            try {
                SchoolCategory::query()->updateOrCreate(
                    ['name' => $school_category],
                    ['name' => $school_category]
                );
            } catch (\Throwable $th) {
                $this->info("\nSCHOOL CATEGORIES === ". $th->getMessage());
            }
        }

        //GENDER
        foreach ($genders as $gender) {
            try {
                SchoolCategory::query()->updateOrCreate(
                    ['name' => $gender],
                    ['name' => $gender]
                );
            } catch (\Throwable $th) {
                $this->info("\nGENDER ERRORS === ". $th->getMessage());
            }
        }

        //PROGRAMMES
        foreach ($programmes as $key => $value) {
            try {
                Programme::query()->updateOrCreate(
                    ['name' => $key],
                    ['code' => $key, 'name' => $value]
                );
            } catch (\Throwable $th) {
                $this->info("\nPROGRAMMES ERROR  === ". $th->getMessage());
            }
        }
    }
}
