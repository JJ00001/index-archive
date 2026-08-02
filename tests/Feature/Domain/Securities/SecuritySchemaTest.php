<?php

use App\Domain\Securities\Models\Security;
use App\Domain\Securities\Models\SecurityIdentifier;
use Illuminate\Database\QueryException;

it('stores source-independent canonical security identifiers', function () {
    $security = Security::factory()->create();
    $identifier = SecurityIdentifier::factory()->for($security)->create([
        'scheme' => 'isin',
        'value' => 'US0378331005',
    ]);

    expect($identifier->security->is($security))->toBeTrue()
        ->and($security->identifiers)->toHaveCount(1)
        ->and($security->identifiers->first()?->scheme)->toBe('isin')
        ->and($security->identifiers->first()?->value)->toBe('US0378331005');
});

it('uniquely identifies canonical securities by normalized scheme and value', function () {
    SecurityIdentifier::factory()->create([
        'scheme' => 'isin',
        'value' => 'US0378331005',
    ]);

    expect(fn () => SecurityIdentifier::factory()->create([
        'scheme' => 'isin',
        'value' => 'US0378331005',
    ]))->toThrow(QueryException::class);
});

it('allows the same normalized value under a different identifier scheme', function () {
    SecurityIdentifier::factory()->create([
        'scheme' => 'isin',
        'value' => 'ABC123',
    ]);
    SecurityIdentifier::factory()->create([
        'scheme' => 'cusip',
        'value' => 'ABC123',
    ]);

    expect(SecurityIdentifier::query()->where('value', 'ABC123')->count())->toBe(2);
});

it('protects canonical identifiers from losing their security', function () {
    $security = Security::factory()->create();
    SecurityIdentifier::factory()->for($security)->create();

    expect(fn () => $security->delete())->toThrow(QueryException::class);
});
