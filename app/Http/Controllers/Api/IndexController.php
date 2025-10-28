<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Index;

class IndexController extends Controller
{
    public function top()
    {
        $indices = Index::limit(5)->get(['id', 'name']);

        return response()->json($indices);
    }
}
