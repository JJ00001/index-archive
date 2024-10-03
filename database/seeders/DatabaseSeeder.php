<?php

namespace Database\Seeders;

use App\Http\Services\HoldingDataService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

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
            $holdingDataService->writeHoldingDataToDB($filename);
            dump('Seeded ' . $filename);
        }
    }
}
