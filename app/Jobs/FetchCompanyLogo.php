<?php

namespace App\Jobs;

use App\Http\Services\CompanyLogoService;
use App\Models\Company;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class FetchCompanyLogo implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Company $company)
    {
        //
    }

    public function uniqueId(): string
    {
        return $this->company->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $companyLogoService = new CompanyLogoService($this->company);

        Log::info("Starting logo fetch: {$this->company->name} ({$this->company->ticker})");

        $logoUrl = $companyLogoService->fetchLogo();

        if ($logoUrl) {
            $companyLogoService->storeLogo();

            Log::info("Finished logo fetch: {$this->company->name} ({$this->company->ticker})");
        } else {
            Log::warning("Finished logo fetch: None found {$this->company->name} ({$this->company->ticker})");
        }
    }
}
