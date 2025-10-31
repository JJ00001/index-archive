<?php

namespace App\Http\Services\WeightHistory;

class WeightHistoryService
{
    public function __construct(protected WeightHistoryStrategy $strategy) {}

    public function getWeightHistory(int $id, int $indexId): array
    {
        return $this->strategy->getWeightHistory($id, $indexId);
    }
}
