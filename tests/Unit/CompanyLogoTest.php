<?php

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('returns the stored logo when a matching file exists', function () {
    Storage::fake('public');

    Storage::disk('public')->put('logos/US1234567890.png', 'logo');

    $company = Company::factory()->create([
        'isin' => 'US1234567890',
    ]);

    expect($company->logo)->toBe('../storage/logos/US1234567890.png');
});

it('returns the brandfetch logo url when no stored logo exists', function () {
    config(['app.brandfetch_api_key' => 'test-client']);

    $company = Company::factory()->create([
        'isin' => 'US1234567890',
    ]);

    expect($company->logo)->toBe('https://cdn.brandfetch.io/US1234567890/fallback/404?c=test-client');
});
