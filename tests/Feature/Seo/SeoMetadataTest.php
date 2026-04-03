<?php

use App\Models\Company;
use App\Models\Country;
use App\Models\Exchange;
use App\Models\Index;
use App\Models\IndexProvider;
use App\Models\Sector;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    config([
        'app.url' => 'https://indexarchive.org',
    ]);

    URL::forceRootUrl(config('app.url'));
    URL::forceScheme('https');
});

function getSeoPage(string $uri)
{
    return test()
        ->withServerVariables([
            'HTTP_HOST' => 'indexarchive.org',
            'HTTPS' => 'on',
        ])
        ->get($uri);
}

it('renders homepage seo metadata', function () {
    $response = getSeoPage('/');

    $response->assertOk();
    $response->assertSee('<title inertia>Track global index composition - IndexArchive</title>', false);
    $response->assertSee('<meta name="description" content="IndexArchive tracks how major market indices change over time across countries, sectors, and companies.">',
        false);
    $response->assertSee('href="https://indexarchive.org"', false);
    $response->assertSee('<meta name="robots" content="max-snippet:-1,max-image-preview:large,max-video-preview:-1">',
        false);
    $response->assertSee('favicon.svg', false);
});

it('renders companies index seo metadata', function () {
    $response = getSeoPage('/companies');

    $response->assertOk();
    $response->assertSee('<title inertia>Browse companies - IndexArchive</title>', false);
    $response->assertSee('Explore companies that appear in tracked market indices and compare their sector, country, and exchange footprint.',
        false);
    $response->assertSee('href="https://indexarchive.org/companies"', false);
});

it('renders indices index seo metadata', function () {
    $response = getSeoPage('/indices');

    $response->assertOk();
    $response->assertSee('<title inertia>Browse indices - IndexArchive</title>', false);
    $response->assertSee('Explore tracked market indices and compare their providers, constituents, sector weights, and country exposure.',
        false);
    $response->assertSee('href="https://indexarchive.org/indices"', false);
});

it('renders company detail seo metadata', function () {
    $company = Company::factory()
        ->for(Sector::factory()->state(['name' => 'Technology']))
        ->for(Country::factory()->state(['name' => 'United States']))
        ->for(Exchange::factory()->state(['name' => 'NASDAQ']))
        ->create([
            'name' => 'Apple',
            'ticker' => 'AAPL',
        ]);

    $response = getSeoPage('/companies/'.$company->id);

    $response->assertOk();
    $response->assertSee('<title inertia>Apple - IndexArchive</title>', false);
    $response->assertSee('Apple (AAPL) is a Technology company listed on NASDAQ in United States. Explore its index memberships and market footprint on IndexArchive.',
        false);
    $response->assertSee('href="https://indexarchive.org/companies/'.$company->id.'"', false);
    $response->assertSee('"@type":"BreadcrumbList"', false);
    $response->assertSee('https:\/\/indexarchive.org\/companies', false);
});

it('renders index detail seo metadata', function () {
    $index = Index::factory()
        ->for(IndexProvider::factory()->state(['name' => 'MSCI']))
        ->create([
            'name' => 'MSCI World',
            'currency' => 'USD',
        ]);

    $response = getSeoPage('/indices/'.$index->id);

    $response->assertOk();
    $response->assertSee('<title inertia>MSCI World - IndexArchive</title>', false);
    $response->assertSee('MSCI World is managed by MSCI reported in USD with 0 current holdings. Explore its constituents, sector allocation, country exposure, and recent composition changes on IndexArchive.',
        false);
    $response->assertSee('href="https://indexarchive.org/indices/'.$index->id.'"', false);
    $response->assertSee('"@type":"BreadcrumbList"', false);
    $response->assertSee('https:\/\/indexarchive.org\/indices', false);
});

it('marks query variants as noindex follow', function () {
    $response = getSeoPage('/companies?sort=-name');

    $response->assertOk();
    $response->assertSee('<meta name="robots" content="noindex,follow">', false);
    $response->assertSee('href="https://indexarchive.org/companies"', false);
});

it('renders a sitemap for the public pages', function () {
    $company = Company::factory()->create();
    $index = Index::factory()->create();

    $response = getSeoPage('/sitemap.xml');

    $response->assertOk();
    $response->assertHeader('content-type', 'text/xml; charset=UTF-8');
    $response->assertSee('https://indexarchive.org', false);
    $response->assertSee('https://indexarchive.org/companies', false);
    $response->assertDontSee('https://indexarchive.org/companies/'.$company->id, false);
    $response->assertSee('https://indexarchive.org/indices/'.$index->id, false);
});

it('ships a crawlable robots file', function () {
    $robots = file_get_contents(public_path('robots.txt'));

    expect($robots)->toContain('Allow: /')
        ->toContain('Disallow: /api/')
        ->toContain('Disallow: /up')
        ->toContain('Sitemap: https://indexarchive.org/sitemap.xml');
});
