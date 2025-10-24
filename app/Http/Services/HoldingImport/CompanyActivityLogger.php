<?php

namespace App\Http\Services\HoldingImport;

use App\Http\Services\HoldingImport\DTOs\CompanyData;
use App\Models\Company;

class CompanyActivityLogger
{
    private static array $fieldsToCheck = [
        'ticker',
        'name',
        'sector_id',
        'country_id',
        'exchange_id',
        'currency_id',
        'asset_class_id',
    ];

    public static function logChanges(Company $existing, CompanyData $new): void
    {
        $newArray = $new->toArray();
        $changes = [];

        foreach (self::$fieldsToCheck as $field) {
            if ($newArray[$field] !== $existing->$field) {
                $changes[$field] = [
                    'old' => $existing->$field,
                    'new' => $newArray[$field],
                ];
            }
        }

        if (! empty($changes)) {
            activity()
                ->performedOn($existing)
                ->withProperties(['changes' => $changes])
                ->log('company_updated');
        }
    }
}
