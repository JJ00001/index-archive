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
    ) {}

    /**
     * @throws JsonException
     */
    public function processHoldingFile(string $fullFilePath): void
    {
        $index = $this->extractIndexFromPath($fullFilePath);

        [$companies, $marketData] = $this->parser->parse($index, $fullFilePath);

        $this->companyUpsert->upsert($companies);

        $this->indexHoldingService->createForIndex($index, $companies);

        $this->marketDataService->insert($index, $marketData);
    }

    private function extractIndexFromPath(string $fullFilePath): Index
    {
        $pathParts = explode('/', $fullFilePath);
        $indexId = $pathParts[array_search('holdingsData', $pathParts) + 1];

        return Index::findOrFail($indexId);
    }
}
