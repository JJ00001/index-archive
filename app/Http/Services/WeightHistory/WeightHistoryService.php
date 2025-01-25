<?php

namespace App\Http\Services\WeightHistory;

use Illuminate\Support\Collection;

class WeightHistoryService
{
    public function __construct(protected WeightHistoryStrategy $strategy)
    {
    }

    public function getWeightHistory(int $id): array
    {
        return $this->strategy->getWeightHistory($id);
    }

    public function getMultipleWeightHistory(Collection $models): array
    {
        return $this->strategy->getMultipleWeightHistory($models);
    }
}
