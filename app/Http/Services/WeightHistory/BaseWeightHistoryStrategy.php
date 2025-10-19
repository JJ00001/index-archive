<?php

namespace App\Http\Services\WeightHistory;

use Illuminate\Support\Collection;

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

    public function getMultipleWeightHistory(Collection $models): array
    {
        $dates = collect();
        $weights = collect();

        foreach ($models as $model) {
            $marketData = $this->fetchWeightHistory($model->id);

            $dateWeightMap = collect($marketData)
                ->keyBy('date')
                ->map(fn ($item) => $item->weight);

            $dates = $dates->merge($dateWeightMap->keys());
            $weights[$model->id] = $dateWeightMap;
        }

        $sortedDates = $dates->unique()->sort()->values();

        $datasets = $models->mapWithKeys(function ($model) use ($sortedDates, $weights) {
            $weights = $sortedDates
                ->map(fn ($date) => $weights[$model->id]->get($date, 0))
                ->toArray();

            return [$model->name => $weights];
        });

        return [
            'labels' => $sortedDates->toArray(),
            'datasets' => $datasets->toArray(),
        ];
    }
}
