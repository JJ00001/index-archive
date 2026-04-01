<?php

namespace App\Http\Controllers;

use App\Models\Index;
use Spatie\Sitemap\Sitemap;

class SitemapController extends Controller
{
    public function __invoke(): Sitemap
    {
        return Sitemap::create()
            ->add(url('/'))
            ->add(route('companies.index'))
            ->add(route('indices.index'))
            ->add(Index::query()->select(['id', 'updated_at', 'created_at'])->cursor());
    }
}
