<?php

namespace App\Http\Services\WeightHistory;

interface WeightHistoryStrategy
{
    public function getWeightHistory(int $id, int $indexId): array;

    public function fetchWeightHistory(int $id, int $indexId): array;
}
