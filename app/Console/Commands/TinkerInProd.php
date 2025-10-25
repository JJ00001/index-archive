<?php

namespace App\Console\Commands;

use App\Jobs\WriteMarketDataJsonToDatabase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class TinkerInProd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:tinker-in-prod';

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
        $files = glob('storage/holdingsData/1/*.json');

        $jobs = [];
        foreach ($files as $file) {
            $jobs[] = new WriteMarketDataJsonToDatabase($file);
        }

        Bus::chain($jobs)->dispatch();
    }
}
