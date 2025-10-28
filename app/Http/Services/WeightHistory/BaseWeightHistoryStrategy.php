<?php

namespace App\Http\Services\WeightHistory;

abstract class BaseWeightHistoryStrategy implements WeightHistoryStrategy
{
    public function getWeightHistory(int $id): array
    {
        $marketData = $this->fetchWeightHistory($id);

        return [
            'labels' => array_map(fn ($data) => $data->date, $marketData),
            'datasets' => [
                'weight' => array_map(fn ($data) => $data->weight, $marketData),
            ],
        ];
    }
}
