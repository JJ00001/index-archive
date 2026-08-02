<?php

use App\Domain\Sec\NPort\Models\FundReport;
use App\Domain\Sec\NPort\Models\Position;
use App\Domain\Sec\NPort\Models\PositionIdentifier;
use App\Domain\Securities\Models\Security;
use Illuminate\Database\QueryException;

it('stores every core holding value in the as-filed hierarchy', function () {
    $report = FundReport::factory()->create();
    $security = Security::factory()->create();
    $position = Position::factory()->for($report)->for($security)->create([
        'sec_holding_id' => '92233720368547758081234567890123456789',
        'issuer_name' => 'Example Issuer',
        'issuer_lei' => '5493001KJTIIGC8Y1R12',
        'issuer_title' => 'Example Notes',
        'issuer_cusip' => '000000000',
        'balance' => '123456789012345678901234.123456789012',
        'unit' => 'PA',
        'other_unit_description' => null,
        'currency_code' => 'USD',
        'currency_value' => '-.040000000000',
        'exchange_rate' => '.960000000000',
        'percentage' => '12.345678901234',
        'payoff_profile' => 'Long',
        'asset_category' => 'DBT',
        'other_asset_description' => null,
        'issuer_type' => 'CORP',
        'other_issuer_description' => null,
        'investment_country' => 'US',
        'is_restricted_security' => true,
        'fair_value_level' => '1',
        'derivative_category' => null,
    ]);

    expect($position->fundReport->is($report))->toBeTrue()
        ->and($position->fundReport->filing->is($report->filing))->toBeTrue()
        ->and($report->positions)->toHaveCount(1)
        ->and($position->security?->is($security))->toBeTrue()
        ->and($position->refresh()->balance)->toBe('123456789012345678901234.123456789012')
        ->and($position->currency_value)->toBe('-0.040000000000')
        ->and($position->exchange_rate)->toBe('0.960000000000')
        ->and($position->percentage)->toBe('12.345678901234')
        ->and($position->is_restricted_security)->toBeTrue();
});

it('preserves sparse derivative positions with no identifiers', function () {
    $position = Position::factory()->sparseSwap()->create();

    expect($position->security)->toBeNull()
        ->and($position->issuer_name)->toBeNull()
        ->and($position->currency_value)->toBeNull()
        ->and($position->derivative_category)->toBe('SWP')
        ->and($position->identifiers)->toHaveCount(0);
});

it('preserves multiple as-filed identifier rows for one position', function () {
    $position = Position::factory()->create();
    $isin = PositionIdentifier::factory()->for($position)->create([
        'sec_identifiers_id' => '10000000000000000000000000000000000001',
        'identifier_isin' => 'US0378331005',
    ]);
    $other = PositionIdentifier::factory()->for($position)->create([
        'sec_identifiers_id' => '10000000000000000000000000000000000002',
        'identifier_isin' => null,
        'identifier_ticker' => 'AAPL',
        'other_identifier' => '000000000',
        'other_identifier_description' => 'CUSIP',
    ]);

    expect($position->identifiers)->toHaveCount(2)
        ->and($isin->position->is($position))->toBeTrue()
        ->and($other->other_identifier)->toBe('000000000');
});

it('enforces the position source key', function () {
    $report = FundReport::factory()->create();
    Position::factory()->for($report)->create(['sec_holding_id' => '123']);

    expect(fn () => Position::factory()->for($report)->create(['sec_holding_id' => '123']))
        ->toThrow(QueryException::class);
});

it('enforces the as-filed identifier source key', function () {
    $position = Position::factory()->create();
    PositionIdentifier::factory()->for($position)->create(['sec_identifiers_id' => '456']);

    expect(fn () => PositionIdentifier::factory()->for($position)->create(['sec_identifiers_id' => '456']))
        ->toThrow(QueryException::class);
});

it('protects reports that own as-filed positions', function () {
    $report = FundReport::factory()->create();
    Position::factory()->for($report)->create();

    expect(fn () => $report->delete())->toThrow(QueryException::class);
});

it('protects positions that own as-filed identifier rows', function () {
    $position = Position::factory()->create();
    PositionIdentifier::factory()->for($position)->create();

    expect(fn () => $position->delete())->toThrow(QueryException::class);
});

it('nulls an optional canonical link when its security is deleted', function () {
    $security = Security::factory()->create();
    $position = Position::factory()->for($security)->create();

    $security->delete();

    expect($position->refresh()->security_id)->toBeNull();
});
