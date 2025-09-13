<?php

namespace App\Jobs;

use App\Http\Services\ScrapeMarketDataService;
use App\Models\Index;
use DateMalformedStringException;
use DateTime;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use JsonException;
use Random\RandomException;

class ScrapeHoldingDataJob implements ShouldQueue
{

    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Index $index,
        public DateTime $startDate,
        public DateTime $endDate
    ) {
        //
    }

    /**
     * Execute the job.
     *
     * @throws DateMalformedStringException
     * @throws GuzzleException
     * @throws RandomException
     * @throws JsonException
     */
    public function handle(): void
    {
        Log::info('Starting scrape job for index: '.$this->index->name);

        $service = new ScrapeMarketDataService($this->index, $this->startDate, $this->endDate);
        $service->scrape();

        Log::info('Scrape job completed for index: '.$this->index->name);
    }

}
