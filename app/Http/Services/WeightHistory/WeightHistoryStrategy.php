<?php

namespace App\Http\Services\WeightHistory;

interface WeightHistoryStrategy
{
    public function getWeightHistory(int $id): array;

    public function fetchWeightHistory(int $id): array;
}
