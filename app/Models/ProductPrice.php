<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPrice extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'value',
        'precision'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function price(): string
    {
        if (! (int)$this->attributes['precision']) {
            return $this->attributes['value'];
        }

        $precisionValue = 1;
        for($i = 1; $i <= $this->attributes['precision']; $i++) {
            $precisionValue = $precisionValue * 10;
        }

        return $this->attributes['value'] / $precisionValue;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
