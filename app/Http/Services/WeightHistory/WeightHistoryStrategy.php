<?php

namespace App\Http\Services\WeightHistory;

interface WeightHistoryStrategy
{
    public function getWeightHistory(int $id): array;
}
