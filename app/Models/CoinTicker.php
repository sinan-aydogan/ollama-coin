<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoinTicker extends Model
{
    protected $fillable = [
        'coin_id',
        'open',
        'high',
        'low',
        'last',
        'average',
        'bid',
        'ask',
        'volume',
        'price_at',
        'price_change',
        'price_change_percent',
    ];

    protected $casts = [
        'price_at' => 'datetime',
    ];

    public function coin(): BelongsTo
    {
        return $this->belongsTo(Coin::class);
    }
}
