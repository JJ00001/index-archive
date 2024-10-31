<?php

namespace App\Jobs;

use App\Http\Services\CompanyLogoService;
use App\Models\Company;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchCompanyLogo implements ShouldQueue, ShouldBeUnique
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

        $logoUrl = $companyLogoService->fetchLogo();

        dd($logoUrl);
//        if ($logoUrl) {
//            $companyLogoService->storeLogo($logoUrl);
//        } else {
//            dd('No logo found');
//        }
    }
}
