<?php

namespace App\Http\Resources;

use App\Models\Index;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class IndexHoldingCountryCollection extends ResourceCollection
{

    public static $wrap = null;

    public function __construct(public Index $index)
    {
        parent::__construct($index->countryStats());
    }

    public function toArray(Request $request): array
    {
        return $this->collection->toArray();
    }
}
