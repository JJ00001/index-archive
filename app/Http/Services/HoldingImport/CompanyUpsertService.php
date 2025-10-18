<?php

namespace App\Http\Services\HoldingImport;

use App\Http\Services\HoldingImport\DTOs\CompanyData;
use App\Models\Company;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CompanyUpsertService
{
    private array $fieldsToCheck = [
        'ticker',
        'name',
        'sector_id',
        'country_id',
        'exchange_id',
        'currency_id',
        'asset_class_id',
    ];

    public function upsert(Collection $companies): void
    {
        Log::info('Upserting companies...');

        $existingCompanies = $this->getExistingCompanies($companies);
        $companiesToUpsert = $this->filterCompaniesForUpsert($companies, $existingCompanies);

        if (! empty($companiesToUpsert)) {
            $this->performUpsert($companiesToUpsert);
        }
    }

    private function hasChanged(Company $existing, CompanyData $new): bool
    {
        $newArray = $new->toArray();

        foreach ($this->fieldsToCheck as $field) {
            if ($newArray[$field] !== $existing->$field) {
                return true;
            }
        }

        return false;
    }

    private function logChanges(Company $existing, CompanyData $new): void
    {
        $newArray = $new->toArray();

        foreach ($this->fieldsToCheck as $field) {
            if ($newArray[$field] !== $existing->$field) {
                Log::info("Field $field changed for ISIN: {$new->isin} ({$existing->$field} -> {$newArray[$field]})");
            }
        }
    }

    private function getExistingCompanies(Collection $companies): Collection
    {
        $isins = $companies->pluck('isin')->toArray();

        return Company::withoutGlobalScopes()
            ->whereIn('isin', $isins)
            ->get()
            ->keyBy('isin');
    }

    private function filterCompaniesForUpsert(Collection $companies, Collection $existingCompanies): array
    {
        $companiesToUpsert = [];

        foreach ($companies as $companyData) {
            $existing = $existingCompanies[$companyData->isin] ?? null;

            if (! $existing) {
                $companiesToUpsert[] = $companyData->toArray();
            } elseif ($this->hasChanged($existing, $companyData)) {
                $companiesToUpsert[] = $companyData->toArray();
                $this->logChanges($existing, $companyData);
            }
        }

        return $companiesToUpsert;
    }

    private function performUpsert(array $companies): void
    {
        Log::info('Upserting '.count($companies).' companies');

        Company::withoutGlobalScopes()->upsert(
            $companies,
            ['isin'],
            array_merge($this->fieldsToCheck, ['updated_at'])
        );
    }
}
