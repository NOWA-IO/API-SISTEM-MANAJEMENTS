<?php

namespace App\Models;

use App\Traits\HasFormattedTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeEarning extends Model
{
    use HasFactory, HasUuids, HasFormattedTimestamps;

    protected $table = 'employee_earnings';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'income',
        'total_income',
    ];

    protected function casts(): array
    {
        return [
            'income' => 'decimal:2',
            'total_income' => 'decimal:2',
        ];
    }

    /**
     * Get the employee (user) that owns the earning record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
