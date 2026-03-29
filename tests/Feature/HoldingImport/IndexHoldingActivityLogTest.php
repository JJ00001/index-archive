<?php

use App\Http\Services\HoldingImport\IndexHoldingActivityLogger;
use App\Models\Company;
use App\Models\Index;
use App\Models\IndexHolding;
use App\Models\IndexProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;

uses(RefreshDatabase::class);

beforeEach(function () {
    $indexProvider = IndexProvider::factory()->create();
    $this->index = Index::factory()->create(['name' => 'MSCI World', 'index_provider_id' => $indexProvider->id]);
    $this->logger = new IndexHoldingActivityLogger;
    $this->date = '2025-01-15';
});

describe('Activity Logging for Additions', function () {
    it('logs company addition with company id and date', function () {
        $company = Company::factory()->create();

        $this->logger->logAdditions($this->index, collect([$company]), $this->date);

        expect(Activity::count())->toBe(1);

        $activity = Activity::first();
        expect($activity->description)->toBe('company_added_to_index');
        expect($activity->subject_type)->toBe(IndexHolding::class);
        expect($activity->properties->get('company_id'))->toBe($company->id);
        expect($activity->properties->get('index_id'))->toBe($this->index->id);
        expect($activity->properties->get('date'))->toBe($this->date);
    });

    it('logs multiple additions in batch', function () {
        $companies = Company::factory()->count(3)->create();

        $this->logger->logAdditions($this->index, $companies, $this->date);

        expect(Activity::count())->toBe(3);
        expect(Activity::where('description', 'company_added_to_index')->count())->toBe(3);
    });
});

describe('Activity Logging for Removals', function () {
    it('logs company removal with company id and date', function () {
        $company = Company::factory()->create();

        $this->logger->logRemovals($this->index, collect([$company]), $this->date);

        expect(Activity::count())->toBe(1);

        $activity = Activity::first();
        expect($activity->description)->toBe('company_removed_from_index');
        expect($activity->subject_type)->toBe(IndexHolding::class);
        expect($activity->properties->get('company_id'))->toBe($company->id);
        expect($activity->properties->get('index_id'))->toBe($this->index->id);
        expect($activity->properties->get('date'))->toBe($this->date);
    });

    it('logs multiple removals in batch', function () {
        $companies = Company::factory()->count(3)->create();

        $this->logger->logRemovals($this->index, $companies, $this->date);

        expect(Activity::count())->toBe(3);
        expect(Activity::where('description', 'company_removed_from_index')->count())->toBe(3);
    });
});

describe('Activity Retrieval', function () {
    it('retrieves activities for a specific index', function () {
        $company = Company::factory()->create();

        $this->logger->logAdditions($this->index, collect([$company]), $this->date);

        $activities = Activity::where('properties->index_id', $this->index->id)
            ->orderBy('created_at', 'desc')
            ->get();

        expect($activities)->toHaveCount(1);
        expect($activities->first()->properties->get('company_id'))->toBe($company->id);
    });

    it('logs both additions and removals in same import', function () {
        $addedCompany = Company::factory()->create(['name' => 'Nvidia Corp.']);
        $removedCompany = Company::factory()->create(['name' => 'Intel Corp.']);

        $this->logger->logAdditions($this->index, collect([$addedCompany]), $this->date);
        $this->logger->logRemovals($this->index, collect([$removedCompany]), $this->date);

        expect(Activity::count())->toBe(2);
        expect(Activity::where('description', 'company_added_to_index')->count())->toBe(1);
        expect(Activity::where('description', 'company_removed_from_index')->count())->toBe(1);
    });

    it('returns index activities sorted by month and status', function () {
        $septemberAddedCompany   = Company::factory()->create(['name' => 'September Added']);
        $septemberRemovedCompany = Company::factory()->create(['name' => 'September Removed']);
        $augustAddedCompany      = Company::factory()->create(['name' => 'August Added']);
        $augustRemovedCompany    = Company::factory()->create(['name' => 'August Removed']);

        $this->logger->logRemovals($this->index, collect([$septemberRemovedCompany]), '2025-10-15');
        $this->logger->logAdditions($this->index, collect([$augustAddedCompany]), '2025-09-15');
        $this->logger->logAdditions($this->index, collect([$septemberAddedCompany]), '2025-10-15');
        $this->logger->logRemovals($this->index, collect([$augustRemovedCompany]), '2025-09-15');

        $response = $this->get(route('indices.show', $this->index));

        $response->assertSuccessful();

        $activities = collect($response->inertiaProps('activities'));

        expect($activities->pluck('description')->all())->toBe([
            'company_added_to_index',
            'company_removed_from_index',
            'company_added_to_index',
            'company_removed_from_index',
        ]);

        expect($activities->pluck('date')->all())->toBe([
            'Sep 2025',
            'Sep 2025',
            'Aug 2025',
            'Aug 2025',
        ]);

        expect($activities->pluck('company.name')->all())->toBe([
            'September Added',
            'September Removed',
            'August Added',
            'August Removed',
        ]);
    });
});
