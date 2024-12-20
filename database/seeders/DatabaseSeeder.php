<?php

namespace Database\Seeders;

use App\Http\Services\HoldingDataService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $holdingDataService = new HoldingDataService();

        $files = File::files(storage_path('holdingsData/'));

        $filenames = array_map(function ($file) {
            return $file->getFilename();
        }, $files);

        foreach ($filenames as $filename) {
            Log::info('Starting seeding ' . $filename);
            $holdingDataService->writeHoldingDataToDB($filename);
        }
    }
}
