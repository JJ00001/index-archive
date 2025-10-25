<?php

namespace App\Http\Services\HoldingImport;

use App\Models\Index;
use JsonException;

class HoldingDataOrchestrator
{
    public function __construct(
        private HoldingFileParser $parser,
        private CompanyUpsertService $companyUpsert,
        private IndexHoldingService $indexHoldingService,
        private MarketDataService $marketDataService,
        private IndexHoldingChangeDetector $changeDetector,
        private IndexHoldingActivityLogger $activityLogger,
    ) {}

    /**
     * @throws JsonException
     */
    public function processHoldingFile(string $fullFilePath): void
    {
        $index = $this->extractIndexFromPath($fullFilePath);
        $date = $this->extractDateFromPath($fullFilePath);

        [$companiesFromFile, $marketDataFromFile] = $this->parser->parse($index, $fullFilePath);

        $this->companyUpsert->upsert($companiesFromFile);

        $changes = $this->changeDetector->detectChanges($index, $companiesFromFile);

        $this->indexHoldingService->createForIndex($index, $companiesFromFile);

        $this->marketDataService->insert($index, $marketDataFromFile);

        $this->logChanges($index, $changes, $date);
    }

    private function extractIndexFromPath(string $fullFilePath): Index
    {
        $pathParts = explode('/', $fullFilePath);
        $indexId = $pathParts[array_search('holdingsData', $pathParts) + 1];

        return Index::findOrFail($indexId);
    }

    private function extractDateFromPath(string $fullFilePath): string
    {
        return pathinfo(basename($fullFilePath), PATHINFO_FILENAME);
    }

    private function logChanges(Index $index, array $changes, string $date): void
    {
        if ($changes['additions']->isNotEmpty()) {
            $this->activityLogger->logAdditions($index, $changes['additions'], $date);
        }

        if ($changes['removals']->isNotEmpty()) {
            $this->activityLogger->logRemovals($index, $changes['removals'], $date);
        }
    }
}
