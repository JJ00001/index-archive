<?php

namespace App\Http\Controllers;

use App\Models\Index;

class IndexController extends Controller
{

    public function index()
    {
        $indices = Index::all();

        return inertia('Index/IndexIndex', [
            'indices' => $indices,
        ]);
    }

    public function top()
    {
        $indices = Index::limit(5)->get(['id', 'name']);

        return response()->json($indices);
    }

}
