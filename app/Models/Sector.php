<?php

namespace App\Models;

use App\Models\Scopes\HasActiveCompanyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy(HasActiveCompanyScope::class)]
class Sector extends Model
{

    use HasFactory;
    
    protected $fillable = [
        'name',
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'sector_id');
    }

    public function scopeWithCompaniesCount(Builder $query): void
    {
        $query->withCount('companies');
    }

    public function scopeWithWeight(Builder $query): void
    {
        $query->addSelect([
            'weight' => function ($query) {
                $query->selectRaw('SUM(market_data.weight)')
                    ->from('companies')
                    ->join('index_holdings', 'index_holdings.company_id', '=', 'companies.id')
                    ->join('market_data', function ($join) {
                        $join->on('index_holdings.id', '=', 'market_data.index_holding_id')
                            ->where('market_data.date', MarketData::maxDate());
                    })
                    ->whereColumn('companies.sector_id', 'sectors.id');
            },
        ]);
    }

    public function scopeWithStats(Builder $query): void
    {
        $query
            ->withCompaniesCount()
            ->withWeight();
    }
}
