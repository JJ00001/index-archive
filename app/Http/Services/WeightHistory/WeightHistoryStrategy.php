<?php

namespace App\Http\Services\WeightHistory;

use Illuminate\Support\Collection;

interface WeightHistoryStrategy
{
    public function getWeightHistory(int $id): array;

    public function fetchWeightHistory(int $id): array;

    public function getMultipleWeightHistory(Collection $models): array;
}
