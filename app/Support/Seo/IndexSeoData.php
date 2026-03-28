<?php

namespace App\Support\Seo;

use App\Models\Index;
use RalphJSmit\Laravel\SEO\Schema\BreadcrumbListSchema;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\Sitemap\Tags\Url;

class IndexSeoData
{

    public static function for(Index $index): SEOData
    {
        $index->loadMissing('indexProvider');
        $index->loadCount('indexHoldings');

        return new SEOData(
            title: $index->name,
            description: self::descriptionFor($index),
            schema: SchemaCollection::initialize()->addBreadcrumbs(
                fn(BreadcrumbListSchema $breadcrumbs): BreadcrumbListSchema => $breadcrumbs->prependBreadcrumbs([
                    'Home' => url('/'),
                    'Indices' => route('indices.index'),
                ])
            )
        );
    }

    protected static function descriptionFor(Index $index): string
    {
        $details = array_filter([
            $index->indexProvider?->name ? "$index->name is managed by {$index->indexProvider->name}" : "$index->name is a tracked market index",
            $index->currency ? "reported in $index->currency" : null,
            isset($index->index_holdings_count) ? "with $index->index_holdings_count current holdings" : null,
        ]);

        return ucfirst(implode(' ',
                $details)).'. Explore its constituents, sector allocation, country exposure, and recent composition changes on IndexArchive.';
    }

    public static function sitemapTagFor(Index $index): Url
    {
        return Url::create(route('indices.show', $index))
                  ->setLastModificationDate($index->updated_at ?? $index->created_at ?? now());
    }

}
