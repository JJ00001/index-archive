<?php

namespace App\Http\Services\WeightHistory;

abstract class BaseWeightHistoryStrategy implements WeightHistoryStrategy
{
    abstract public function getWeightHistory(int $id): array;
}
