<?php

namespace App\Support\Seo;

use App\Models\Company;
use RalphJSmit\Laravel\SEO\Schema\BreadcrumbListSchema;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\Sitemap\Tags\Url;

class CompanySeoData
{

    public static function for(Company $company): SEOData
    {
        $company->loadMissing([
            'country:id,name',
            'exchange:id,name',
            'sector:id,name',
        ]);

        return new SEOData(
            title: $company->name,
            description: self::descriptionFor($company),
            image: $company->logo,
            schema: SchemaCollection::initialize()->addBreadcrumbs(
                fn(BreadcrumbListSchema $breadcrumbs): BreadcrumbListSchema => $breadcrumbs->prependBreadcrumbs([
                    'Home' => url('/'),
                    'Companies' => route('companies.index'),
                ])
            )
        );
    }

    protected static function descriptionFor(Company $company): string
    {
        $details = array_filter([
            $company->ticker ? "$company->name ($company->ticker)" : $company->name,
            $company->sector?->name ? "is a {$company->sector->name} company" : null,
            $company->exchange?->name ? "listed on {$company->exchange->name}" : null,
            $company->country?->name ? "in {$company->country->name}" : null,
        ]);

        return ucfirst(implode(' ', $details)).'. Explore its index memberships and market footprint on IndexArchive.';
    }

    public static function sitemapTagFor(Company $company): Url
    {
        return Url::create(route('companies.show', $company))
                  ->setLastModificationDate($company->updated_at ?? $company->created_at ?? now());
    }

}
