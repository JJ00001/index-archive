<?php

namespace App\Http\Resources;

use App\Models\Index;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class IndexHoldingSectorCollection extends ResourceCollection
{
    public function __construct(public Index $index)
    {
        parent::__construct($index->sectorStats());
    }

    public function toArray(Request $request): array
    {
        return $this->collection->toArray();
    }
}
