<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataSource extends Model
{

    protected $fillable = [
        'index_id',
        'base_url',
        'field_mappings',
    ];

    public function index(): BelongsTo
    {
        return $this->belongsTo(Index::class);
    }
}
