<?php

namespace App\Models;

use App\Traits\HasFormattedTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
<<<<<<< HEAD
=======
use Illuminate\Database\Eloquent\Relations\HasMany;
>>>>>>> 22570f13b564ab9f7273958c4f1ec2607bb21377

class Product extends Model
{
    use HasFactory, HasUuids, HasFormattedTimestamps;

    protected $table = 'products';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'price',
        'weight',
    ];

<<<<<<< HEAD
=======
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
>>>>>>> 22570f13b564ab9f7273958c4f1ec2607bb21377
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'weight' => 'decimal:2',
        ];
    }

    /**
<<<<<<< HEAD
     * Get the user that created/owns the product.
=======
     * Get the user that owns the product.
>>>>>>> 22570f13b564ab9f7273958c4f1ec2607bb21377
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
<<<<<<< HEAD
=======

    /**
     * Get the formulas for the product.
     */
    public function formulas(): HasMany
    {
        return $this->hasMany(Formula::class);
    }
>>>>>>> 22570f13b564ab9f7273958c4f1ec2607bb21377
}
