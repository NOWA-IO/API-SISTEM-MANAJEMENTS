<?php

namespace App\Models;

use App\Traits\HasFormattedTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Formula extends Model
{
    use HasFactory, HasUuids, HasFormattedTimestamps;

    protected $table = 'formulas';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'product_id',
        'description',
    ];

    /**
     * Get the product that owns the formula.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
