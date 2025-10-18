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

        $isins = $companies->pluck('isin')->toArray();
        $existingCompanies = Company::withoutGlobalScopes()
            ->whereIn('isin', $isins)
            ->get()
            ->keyBy('isin');

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

        if (! empty($companiesToUpsert)) {
            Log::info('Upserting '.count($companiesToUpsert).' companies');
            Company::withoutGlobalScopes()->upsert(
                $companiesToUpsert,
                ['isin'],
                array_merge($this->fieldsToCheck, ['updated_at'])
            );
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
}
