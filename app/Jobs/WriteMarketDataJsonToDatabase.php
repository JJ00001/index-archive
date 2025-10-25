<?php

namespace App\Jobs;

use App\Http\Services\HoldingImport\HoldingDataOrchestrator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use JsonException;

class WriteMarketDataJsonToDatabase implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $fullFilePath
    ) {
        //
    }

    /**
     * Execute the job.
     *
     * @throws JsonException
     */
    public function handle(): void
    {
        Log::info('Starting write market data job for file: '.$this->fullFilePath);

        $orchestrator = app(HoldingDataOrchestrator::class);
        $orchestrator->processHoldingFile($this->fullFilePath);

        Log::info('Write market data job completed for file: '.$this->fullFilePath);
    }
}
