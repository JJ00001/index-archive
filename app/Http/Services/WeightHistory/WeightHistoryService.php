<?php

namespace App\Http\Services\WeightHistory;

class WeightHistoryService
{
    public function __construct(protected WeightHistoryStrategy $strategy) {}

    public function getWeightHistory(int $id): array
    {
        return $this->strategy->getWeightHistory($id);
    }
}
