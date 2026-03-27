<?php

use App\Http\Services\CompanyLogoService;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('returns the stored logo url when a logo path is persisted', function () {
    config(['filesystems.default' => 'public']);
    Storage::fake('public');

    $company = Company::factory()->create([
        'isin' => 'US1234567890',
        'has_stored_logo' => true,
    ]);

    expect($company->logo)->toBe(Storage::disk()->url('logos/US1234567890.png'));
});

it('returns the brandfetch logo url when no stored logo exists', function () {
    config(['app.brandfetch_api_key' => 'test-client']);

    $company = Company::factory()->create([
        'isin' => 'US1234567890',
    ]);

    expect($company->logo)->toBe('https://cdn.brandfetch.io/US1234567890/fallback/404?c=test-client');
});

it('stores the logo path on the company when persisting a fetched logo', function () {
    config(['filesystems.default' => 'public']);
    Storage::fake('public');
    Http::fake([
        'https://example.com/logo.png' => Http::response('logo-binary'),
    ]);

    $company = Company::factory()->create([
        'isin' => 'US1234567890',
    ]);

    $service = new CompanyLogoService($company);

    $reflection      = new ReflectionClass($service);
    $logoUrlProperty = $reflection->getProperty('logoUrl');
    $logoUrlProperty->setValue($service, 'https://example.com/logo.png');

    $service->storeLogo();

    expect($company->fresh()->has_stored_logo)->toBeTrue();
    Storage::disk()->assertExists('logos/US1234567890.png');
});
