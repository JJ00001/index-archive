<?php

namespace App\Console\Commands;

use App\Http\Services\HoldingDataService;
use Illuminate\Console\Command;

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
        $hs = new HoldingDataService();
        $hs->scrape();
    }
}
