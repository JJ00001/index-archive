<?php

namespace App\Jobs;

use App\Models\Company;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        try {
            $response = Http::withHeader('X-Api-Key', env('API_NINJA_API_KEY'))
                ->accept('application/json')
                ->get('https://api.api-ninjas.com/v1/logo?ticker=' . $this->company->ticker);

            $logoURL = $response->json()[0]['image'] ?? null;

            if ($logoURL) {
                $response = Http::get($logoURL);
                $logo = $response->body();
                $logoPathInStorage = 'logos/' . $this->company->ticker . '.png';
                Storage::disk('public')->put($logoPathInStorage, $logo);
                $this->company->update(['logo' => $logoPathInStorage]);

                Log::info('Logo fetching complete: ' . $this->company->name);
            }
        } catch (ConnectionException $e) {
            Log::info($e->getMessage());
        }
    }
}
